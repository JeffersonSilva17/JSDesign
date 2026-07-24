(() => {
  "use strict";

  const body = document.body;
  const siteShell = document.querySelector("#site-shell");
  const floatingActions = document.querySelector(".floating-actions");
  const backdrop = document.querySelector("[data-surface-backdrop]");
  const toast = document.querySelector(".toast");
  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  const desktopViewport = window.matchMedia("(min-width: 1101px)");
  const windowNameFlag = "__jsd_promotion_seen__";
  const scrollIntentKeys = new Set([
    "ArrowDown",
    "ArrowUp",
    "End",
    "Home",
    "PageDown",
    "PageUp",
    " ",
  ]);
  const surfaces = new Map(
    [...document.querySelectorAll("[data-surface]")].map((surface) => [
      surface.dataset.surface,
      surface,
    ]),
  );

  let activeSurfaceName = null;
  let activeTrigger = null;
  let toastTimer = 0;
  let engagementCount = 0;
  let promotionEligible = false;
  let promotionSeen = readSessionFlag("jsd-promotion-seen");
  let promotionPendingTimer = 0;
  let userIntentObserved = false;
  let navigationFocusTimer = 0;
  let navigationRequest = 0;

  const catalog = [...document.querySelectorAll("[data-search-name]")].map((card) => ({
    name: card.dataset.searchName,
    terms: card.dataset.searchTerms,
    target: card.id,
    type: card.querySelector(".product-type")?.textContent.trim() || "Produto",
  }));

  function readSessionFlag(key) {
    let sessionFlag = false;
    try {
      sessionFlag = window.sessionStorage.getItem(key) === "true";
    } catch {
      // window.name mantém o sinal na mesma aba quando sessionStorage falha.
    }
    return sessionFlag || window.name.split("|").includes(windowNameFlag);
  }

  function writeSessionFlag(key) {
    try {
      window.sessionStorage.setItem(key, "true");
    } catch {
      // O fallback abaixo continua disponível quando o armazenamento falha.
    }
    if (!window.name.split("|").includes(windowNameFlag)) {
      window.name = [window.name, windowNameFlag].filter(Boolean).join("|");
    }
  }

  function getFocusable(container) {
    return [...container.querySelectorAll(
      'a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])',
    )].filter((element) => !element.hidden && element.getClientRects().length > 0);
  }

  function setBackgroundInert(isInert) {
    siteShell.inert = isInert;
    floatingActions.inert = isInert;
    siteShell.setAttribute("aria-hidden", String(isInert));
    floatingActions.setAttribute("aria-hidden", String(isInert));
  }

  function updateSurfaceTriggers() {
    document.querySelectorAll("[data-open-surface]").forEach((trigger) => {
      const isOpen = trigger.dataset.openSurface === activeSurfaceName;
      if (trigger.hasAttribute("aria-expanded")) {
        trigger.setAttribute("aria-expanded", String(isOpen));
      }
    });
  }

  function focusElement(element) {
    if (!(element instanceof HTMLElement) || !element.isConnected || element.hidden) return;
    const naturallyFocusable = element.matches(
      'a[href], button:not([disabled]), input:not([disabled]), [tabindex]',
    );
    if (!naturallyFocusable) {
      element.setAttribute("tabindex", "-1");
      element.addEventListener("blur", () => element.removeAttribute("tabindex"), { once: true });
    }
    element.focus({ preventScroll: true });
  }

  function findFocusContext() {
    const current = document.activeElement;
    if (
      current instanceof HTMLElement
      && current !== body
      && current !== document.documentElement
      && current.isConnected
    ) {
      return current;
    }

    const viewportAnchor = Math.min(window.innerHeight * 0.34, 320);
    const headings = [...document.querySelectorAll("main h1, main h2")].filter((heading) => {
      const rect = heading.getBoundingClientRect();
      return rect.bottom > 0 && rect.top < window.innerHeight;
    });
    return headings.sort((a, b) =>
      Math.abs(a.getBoundingClientRect().top - viewportAnchor)
      - Math.abs(b.getBoundingClientRect().top - viewportAnchor),
    )[0] || document.querySelector("main");
  }

  function openSurface(name, trigger = document.activeElement) {
    const surface = surfaces.get(name);
    if (!surface) return;
    const returnTarget =
      trigger instanceof HTMLElement
      && trigger !== body
      && trigger !== document.documentElement
        ? trigger
        : findFocusContext();

    if (activeSurfaceName) {
      closeSurface({ restoreFocus: false });
    }

    activeSurfaceName = name;
    activeTrigger = returnTarget;
    surface.hidden = false;
    backdrop.hidden = false;
    body.classList.add("surface-open");
    setBackgroundInert(true);
    updateSurfaceTriggers();

    if (name === "promo") {
      promotionSeen = true;
      writeSessionFlag("jsd-promotion-seen");
    }

    const autofocusTarget =
      name === "search"
        ? surface.querySelector("#panel-search-input")
        : getFocusable(surface)[0];
    window.requestAnimationFrame(() => autofocusTarget?.focus());
  }

  function closeSurface(options = {}) {
    const { restoreFocus = true } = options;
    if (!activeSurfaceName) return;

    const surface = surfaces.get(activeSurfaceName);
    const triggerToRestore = activeTrigger;
    surface.hidden = true;
    backdrop.hidden = true;
    body.classList.remove("surface-open");
    activeSurfaceName = null;
    activeTrigger = null;
    setBackgroundInert(false);
    updateSurfaceTriggers();

    if (restoreFocus && triggerToRestore?.isConnected && !triggerToRestore.hidden) {
      window.requestAnimationFrame(() => focusElement(triggerToRestore));
    }
    queuePromotion();
  }

  function trapFocus(event) {
    if (event.key !== "Tab" || !activeSurfaceName) return;
    const surface = surfaces.get(activeSurfaceName);
    const focusable = getFocusable(surface);
    if (!focusable.length) {
      event.preventDefault();
      return;
    }

    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  }

  function announce(message) {
    window.clearTimeout(toastTimer);
    toast.textContent = message;
    toast.hidden = false;
    toastTimer = window.setTimeout(() => {
      toast.hidden = true;
      toast.textContent = "";
    }, 4200);
  }

  function registerEngagement(weight = 1) {
    engagementCount += weight;
    if (engagementCount >= 2) {
      promotionEligible = true;
      queuePromotion();
    }
  }

  function queuePromotion() {
    if (
      !userIntentObserved
      || !promotionEligible
      || promotionSeen
      || activeSurfaceName
      || promotionPendingTimer
    ) return;
    promotionPendingTimer = window.setTimeout(() => {
      promotionPendingTimer = 0;
      if (
        userIntentObserved
        && promotionEligible
        && !promotionSeen
        && !activeSurfaceName
        && !document.hidden
      ) {
        openSurface("promo");
      }
    }, reducedMotion.matches ? 0 : 700);
  }

  function cancelPendingDestinationFocus() {
    navigationRequest += 1;
    if (navigationFocusTimer) {
      window.clearTimeout(navigationFocusTimer);
      navigationFocusTimer = 0;
    }
  }

  function markUserIntent() {
    userIntentObserved = true;
    cancelPendingDestinationFocus();
  }

  function normalizeText(value) {
    return value
      .normalize("NFD")
      .replace(/\p{Diacritic}/gu, "")
      .toLocaleLowerCase("pt-BR")
      .trim();
  }

  function resultIcon(name) {
    return name
      .split(/\s+/)
      .filter(Boolean)
      .slice(0, 2)
      .map((word) => word[0])
      .join("")
      .toLocaleUpperCase("pt-BR");
  }

  function makeSearchResult(item) {
    const button = document.createElement("button");
    button.type = "button";
    button.className = "search-result";
    button.dataset.searchTarget = item.target;

    const icon = document.createElement("span");
    icon.className = "search-result-icon";
    icon.setAttribute("aria-hidden", "true");
    icon.textContent = resultIcon(item.name);

    const copy = document.createElement("span");
    const title = document.createElement("strong");
    const type = document.createElement("small");
    title.textContent = item.name;
    type.textContent = item.type;
    copy.append(title, type);

    const arrow = document.createElement("span");
    arrow.setAttribute("aria-hidden", "true");
    arrow.textContent = "→";
    button.append(icon, copy, arrow);
    return button;
  }

  function renderSearchResults(term = "") {
    const results = document.querySelector("#search-results");
    const label = document.querySelector("#results-label");
    const normalizedTerm = normalizeText(term);
    results.replaceChildren();

    const matches = normalizedTerm
      ? catalog.filter((item) =>
          normalizeText(`${item.name} ${item.terms}`).includes(normalizedTerm),
        )
      : catalog;

    if (!normalizedTerm) {
      label.textContent = "Sugestões populares";
    } else if (matches.length) {
      label.textContent = `${matches.length} ${matches.length === 1 ? "resultado encontrado" : "resultados encontrados"}`;
    } else {
      label.textContent = "Nenhum produto exato no catálogo";
    }

    if (matches.length) {
      matches.forEach((item) => results.append(makeSearchResult(item)));
      return;
    }

    results.append(makeSearchResult({
      name: `Projeto exclusivo: ${term.trim()}`,
      type: "Sugestão de criação sob medida — não é um produto do catálogo",
      target: "exclusivo",
    }));
  }

  function goToDestination(targetId, options = {}) {
    const { block = "center", closeCurrentSurface = true } = options;
    const target = document.getElementById(targetId);
    if (!target) return;
    if (closeCurrentSurface && activeSurfaceName) {
      closeSurface({ restoreFocus: false });
    }
    cancelPendingDestinationFocus();
    const request = navigationRequest;
    const focusTarget =
      target.matches("h1, h2, h3")
        ? target
        : target.querySelector("h1, h2, h3") || target;
    target.scrollIntoView({
      behavior: reducedMotion.matches ? "auto" : "smooth",
      block,
    });
    const completeNavigation = () => {
      if (request !== navigationRequest) return;
      navigationFocusTimer = 0;
      focusElement(focusTarget);
    };
    if (reducedMotion.matches) {
      completeNavigation();
    } else {
      navigationFocusTimer = window.setTimeout(completeNavigation, 450);
    }
    registerEngagement(2);
  }

  function validEmail(value) {
    if (value.length > 254 || /\s/u.test(value)) return false;
    const parts = value.split("@");
    if (parts.length !== 2) return false;
    const [local, domain] = parts;
    if (
      !local
      || local.length > 64
      || local.startsWith(".")
      || local.endsWith(".")
      || local.includes("..")
      || !domain
      || domain.includes("..")
    ) return false;

    const labels = domain.split(".");
    if (labels.length < 2 || labels.at(-1).length < 2) return false;
    return labels.every((label) =>
      /^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/iu.test(label),
    );
  }

  function handleEmailForm(form) {
    const input = form.querySelector('input[type="email"]');
    const error = form.querySelector(".form-error");
    const value = input.value.trim();

    if (!value) {
      input.setAttribute("aria-invalid", "true");
      error.textContent = "Informe um e-mail para continuar.";
      input.focus();
      return;
    }

    if (!validEmail(value)) {
      input.setAttribute("aria-invalid", "true");
      error.textContent = "Digite um e-mail válido, como voce@exemplo.com.";
      input.focus();
      return;
    }

    input.removeAttribute("aria-invalid");
    error.textContent = "";
    input.value = "";
    announce(
      form.dataset.emailForm === "promo"
        ? "Benefício demonstrativo confirmado. Nenhum dado foi enviado."
        : "Cadastro demonstrativo confirmado. Nenhum dado foi enviado.",
    );

    if (form.dataset.emailForm === "promo") {
      closeSurface();
    }
  }

  document.addEventListener("click", (event) => {
    if (event.isTrusted) markUserIntent();

    const openTrigger = event.target.closest("[data-open-surface]");
    if (openTrigger) {
      event.preventDefault();
      openSurface(openTrigger.dataset.openSurface, openTrigger);
      if (openTrigger.dataset.openSurface === "search") registerEngagement();
      return;
    }

    const closeTrigger = event.target.closest("[data-close-surface]");
    if (closeTrigger) {
      closeSurface();
      return;
    }

    const searchTarget = event.target.closest("[data-search-target]");
    if (searchTarget) {
      goToDestination(searchTarget.dataset.searchTarget);
      return;
    }

    const mobileLink = event.target.closest(".mobile-nav a[href^='#']");
    if (mobileLink) {
      event.preventDefault();
      goToDestination(mobileLink.getAttribute("href").slice(1), { block: "start" });
      return;
    }

    const categoryLink = event.target.closest(".category-card[href^='#']");
    if (categoryLink) {
      event.preventDefault();
      goToDestination(categoryLink.getAttribute("href").slice(1));
      return;
    }

    const favorite = event.target.closest(".favorite-button");
    if (favorite) {
      const product = favorite.dataset.product;
      const selected = favorite.getAttribute("aria-pressed") !== "true";
      favorite.setAttribute("aria-pressed", String(selected));
      favorite.setAttribute(
        "aria-label",
        `${selected ? "Remover" : "Adicionar"} ${product} ${selected ? "dos" : "aos"} favoritos`,
      );
      announce(`${product} ${selected ? "adicionado aos" : "removido dos"} favoritos.`);
      registerEngagement(2);
      return;
    }

    const addCart = event.target.closest(".add-cart");
    if (addCart) {
      const countElement = document.querySelector(".cart-count");
      const cartButton = document.querySelector(".cart-button");
      const nextCount = Number(countElement.textContent) + 1;
      countElement.textContent = String(nextCount);
      const itemLabel = nextCount === 1 ? "1 item" : `${nextCount} itens`;
      cartButton.setAttribute("aria-label", `Carrinho, ${itemLabel}`);
      cartButton.dataset.demoMessage = `Seu carrinho demonstrativo contém ${itemLabel}.`;
      announce(`${addCart.dataset.product} adicionado. Carrinho com ${itemLabel}.`);
      registerEngagement(2);
      return;
    }

    const demoControl = event.target.closest("[data-demo-message]");
    if (demoControl) {
      announce(demoControl.dataset.demoMessage);
      registerEngagement();
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.isTrusted && scrollIntentKeys.has(event.key)) {
      markUserIntent();
    }
    if (event.key === "Escape" && activeSurfaceName) {
      event.preventDefault();
      closeSurface();
      return;
    }
    trapFocus(event);
  });

  backdrop.addEventListener("click", () => closeSurface());

  document.querySelectorAll("[data-email-form]").forEach((form) => {
    form.addEventListener("submit", (event) => {
      event.preventDefault();
      handleEmailForm(form);
    });

    const input = form.querySelector('input[type="email"]');
    input.addEventListener("input", () => {
      if (input.getAttribute("aria-invalid") === "true") {
        input.removeAttribute("aria-invalid");
        form.querySelector(".form-error").textContent = "";
      }
    });
  });

  const searchInput = document.querySelector("#panel-search-input");
  searchInput.addEventListener("input", () => {
    const limitedValue = searchInput.value.slice(0, 120);
    if (searchInput.value !== limitedValue) searchInput.value = limitedValue;
    renderSearchResults(limitedValue);
    registerEngagement();
  });

  const carouselUpdaters = [];
  document.querySelectorAll(".product-scroller[id]").forEach((carousel) => {
    const previousButton = document.querySelector(`[data-carousel-prev="${carousel.id}"]`);
    const nextButton = document.querySelector(`[data-carousel-next="${carousel.id}"]`);
    if (!previousButton || !nextButton) return;

    const updateControls = () => {
      const maximumScroll = Math.max(0, carousel.scrollWidth - carousel.clientWidth);
      const hasOverflow = maximumScroll > 2;
      previousButton.disabled = !hasOverflow || carousel.scrollLeft <= 2;
      nextButton.disabled = !hasOverflow || carousel.scrollLeft >= maximumScroll - 2;
    };

    const moveCarousel = (direction) => {
      carousel.scrollBy({
        left: direction * Math.max(260, carousel.clientWidth * 0.76),
        behavior: reducedMotion.matches ? "auto" : "smooth",
      });
      registerEngagement();
    };

    previousButton.addEventListener("click", () => moveCarousel(-1));
    nextButton.addEventListener("click", () => moveCarousel(1));
    carousel.addEventListener("scroll", updateControls, { passive: true });
    carousel.querySelectorAll("img").forEach((image) => {
      if (!image.complete) image.addEventListener("load", updateControls, { once: true });
    });
    if ("ResizeObserver" in window) {
      const observer = new ResizeObserver(updateControls);
      observer.observe(carousel);
    }
    carouselUpdaters.push(updateControls);
    window.requestAnimationFrame(updateControls);
  });

  window.addEventListener("wheel", (event) => {
    if (event.isTrusted) markUserIntent();
  }, { passive: true });
  window.addEventListener("touchstart", (event) => {
    if (event.isTrusted) markUserIntent();
  }, { passive: true });
  document.addEventListener("pointerdown", (event) => {
    if (event.isTrusted) markUserIntent();
  }, { passive: true });

  window.addEventListener(
    "scroll",
    () => {
      const scrollable = document.documentElement.scrollHeight - window.innerHeight;
      if (userIntentObserved && scrollable > 0 && window.scrollY / scrollable >= 0.22) {
        promotionEligible = true;
        queuePromotion();
      }
    },
    { passive: true },
  );

  document.addEventListener("visibilitychange", () => {
    if (!document.hidden) queuePromotion();
  });

  desktopViewport.addEventListener("change", (event) => {
    if (event.matches && activeSurfaceName === "menu") {
      closeSurface({ restoreFocus: false });
      document.querySelector(".brand")?.focus();
    }
  });

  window.addEventListener("resize", () => {
    if (activeSurfaceName) {
      const surface = surfaces.get(activeSurfaceName);
      surface.scrollTop = Math.min(surface.scrollTop, surface.scrollHeight - surface.clientHeight);
    }
    carouselUpdaters.forEach((updateControls) => updateControls());
  });

  renderSearchResults();
})();
