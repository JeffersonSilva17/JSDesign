import { defaultLocale } from './locales';
import type { PublicContent } from './publicContent.types';

export const publicContent = {
  locale: defaultLocale,
  brand: {
    name: 'JS Designs',
    homeAriaLabel: 'JS Designs, voltar para a home',
    shortSubtitle: 'celebrações autorais',
  },
  metadata: {
    root: {
      title: 'JS Designs',
      description:
        'Loja online JS Designs para lembrancinhas físicas personalizadas, convites digitais personalizados e Produto Digital Pronto.',
    },
    home: {
      title: 'Loja online de personalizados físicos e digitais',
      description:
        'Loja online JS Designs para lembrancinhas físicas personalizadas, convites digitais personalizados e Produto Digital Pronto com experiência mobile-first.',
    },
  },
  navigation: {
    ariaLabel: 'Navegação principal',
    mobileLabel: 'Menu',
    skipToMainContent: 'Pular para o conteúdo principal',
    mainNavItems: [
      { label: 'Home', href: '/', description: 'Voltar para a página inicial da JS Designs.' },
      { label: 'Produtos', href: '/produtos', description: 'Ver a entrada futura para produtos da loja.' },
      { label: 'Categorias', href: '/categorias', description: 'Ver categorias principais da loja.' },
      { label: 'Buscar', href: '/buscar', description: 'Buscar por tema, produto ou ocasião.' },
      { label: 'Carrinho', href: '/carrinho', description: 'Revisar itens escolhidos futuramente.' },
      { label: 'Área da Cliente', href: '/entrar', description: 'Entrar ou criar acesso à Área da Cliente.' },
      { label: 'Suporte', href: '/suporte', description: 'Acessar ajuda dentro do site.' },
    ],
  },
  cta: {
    primary: {
      label: 'Ver produtos',
      href: '/produtos',
      description: 'Começar a descoberta de produtos JS Designs.',
    },
    secondary: {
      label: 'Buscar por tema',
      href: '/buscar',
      description: 'Encontrar ideias por tema, produto ou ocasião.',
    },
  },
  home: {
    hero: {
      eyebrow: 'Loja JS Designs',
      title: 'Detalhes personalizados para celebrar com cuidado.',
      lead:
        'Uma experiência clara para descobrir lembrancinhas físicas personalizadas, convites digitais personalizados e Produto Digital Pronto sem depender de atendimento para começar.',
    },
    mostWanted: {
      eyebrow: 'Logo de cara',
      title: 'Mais procurados',
      description:
        'Esta seleção editorial apresenta as principais modalidades da JS Designs; não é um ranking de vendas ou buscas.',
      items: [
        {
          title: 'Kit Festa Fazendinha',
          modality: 'Produto físico personalizado',
          description:
            'Lembrancinhas físicas personalizadas que terão quantidade, tema, nome, data e outros detalhes definidos antes do carrinho.',
          visualLabel: 'Festa em papel',
          tone: 'soft',
          cta: {
            label: 'Ver opções',
            href: '/produtos',
          },
        },
        {
          title: 'Convite Jardim Dourado',
          modality: 'Convite digital personalizado',
          description:
            'Convites digitais personalizados não são entrega imediata: precisam de edição/criação, prévia e aprovação antes da entrega final.',
          visualLabel: 'Convite autoral',
          tone: 'champagne',
          cta: {
            label: 'Ver modelos',
            href: '/buscar',
          },
        },
        {
          title: 'Topo Céu Encantado',
          modality: 'Produto digital',
          description:
            'Produto Digital Pronto, sem personalização e compatível com Silhouette Studio. Quando a compra estiver disponível, o download imediato será liberado após o pagamento confirmado.',
          visualLabel: 'Arquivo de corte',
          tone: 'calm',
          cta: {
            label: 'Ver arquivo',
            href: '/produtos',
          },
        },
      ],
    },
    nextPaths: {
      eyebrow: 'Próximos caminhos',
      title: 'Continue explorando',
      description:
        'Use Produtos, Categorias ou Buscar para explorar o catálogo público e encontrar opções por tema, produto ou ocasião.',
    },
  },
  catalog: {
    listing: {
      eyebrow: 'Catálogo JS Designs',
      title: 'Produtos',
      description: 'Explore produtos publicados por categoria, ocasião e modalidade.',
      emptyTitle: 'Nenhum produto encontrado',
      emptyDescription: 'Esta combinação de filtros ainda não possui produtos publicados.',
    },
    categories: {
      eyebrow: 'Descobrir por categoria',
      title: 'Categorias',
      description: 'Escolha uma opção disponível no catálogo publicado.',
      empty: 'Ainda não há categorias públicas disponíveis.',
    },
    filters: {
      title: 'Filtrar produtos', clear: 'Limpar filtros', category: 'Categoria',
      occasion: 'Ocasião', modality: 'Tipo',
    },
    card: {
      details: 'Ver detalhes', unavailableImage: 'Imagem indisponível',
      immediate: 'Download imediato', leadTime: 'Prazo de produção',
    },
    pagination: { label: 'Paginação dos produtos', previous: 'Página anterior', next: 'Próxima página' },
    states: {
      invalidTitle: 'Filtros inválidos',
      invalidDescription: 'Revise o endereço ou recomece com todos os produtos.',
      unavailableTitle: 'Catálogo temporariamente indisponível',
      unavailableDescription: 'Não foi possível carregar os produtos agora. Tente novamente em instantes.',
      pageOutOfRangeTitle: 'Página sem produtos',
      pageOutOfRangeDescription: 'Esta página não possui itens, mas há produtos publicados em páginas anteriores.',
      back: 'Ver todos os produtos',
      backToLastPage: 'Voltar para a última página',
    },
    detail: { eyebrow: 'Produto publicado', back: 'Voltar aos produtos' },
    modality: {
      physical_personalized: 'Produto físico personalizado',
      digital_personalized: 'Produto digital personalizado',
      digital_ready: 'Produto digital',
    },
    availability: {
      available: 'Disponível',
      unavailable: 'Indisponível',
      made_to_order: 'Produzido sob encomenda',
    },
    metadata: {
      products: { title: 'Produtos', description: 'Catálogo público de produtos físicos e digitais da JS Designs.' },
      categories: { title: 'Categorias', description: 'Categorias e ocasiões disponíveis no catálogo público da JS Designs.' },
      detail: {
        title: 'Detalhes do produto', description: 'Resumo público de um produto da JS Designs.',
        robots: { index: false, follow: true },
      },
    },
  },
  search: {
    metadata: {
      title: 'Buscar produtos',
      description: 'Busca pública da JS Designs por produto, categoria, tema, ocasião ou tipo.',
    },
    eyebrow: 'Descoberta por intenção',
    title: 'Buscar',
    description: 'Encontre produtos publicados por nome, categoria, tipo, tema ou ocasião.',
    form: {
      label: 'O que você procura?',
      placeholder: 'Ex.: convite de aniversário',
      submit: 'Buscar produtos',
      help: 'Use de 2 a 120 caracteres. Não inclua dados pessoais: o termo ficará no endereço da página.',
      error: 'Revise o termo informado antes de buscar.',
    },
    initial: {
      title: 'Comece por uma ideia',
      description: 'Digite um termo ou explore categorias e ocasiões públicas disponíveis.',
      categories: 'Categorias principais',
      editorialSuggestions: 'Sugestões editoriais',
    },
    exact: { title: 'Resultados exatos', categoryPrefix: 'Categoria' },
    similar: { title: 'Resultados semelhantes', description: 'Estas opções se aproximam do termo informado.' },
    suggestions: { title: 'Você também pode buscar por' },
    termLabel: 'Termo buscado',
    invitation: {
      title: 'Seu tema pode virar um convite personalizado',
      description: 'Os temas de convite não formam uma lista fechada. Explore os convites digitais personalizados disponíveis.',
      action: 'Descobrir convites digitais personalizados',
    },
    states: {
      emptyTitle: 'Nenhum resultado encontrado',
      emptyDescription: 'Tente outro nome, tema, categoria ou ocasião usando as sugestões abaixo.',
      invalidTitle: 'Busca inválida',
      invalidDescription: 'O termo precisa ter entre 2 e 120 caracteres e usar um formato válido.',
      unavailableTitle: 'Busca temporariamente indisponível',
      unavailableDescription: 'Não foi possível consultar os produtos agora. Seu termo continua no formulário para você tentar novamente.',
      outOfRangeTitle: 'Página fora do intervalo',
      outOfRangeDescription: 'Esta página não existe para a busca informada.',
      retry: 'Tentar novamente',
      lastPage: 'Voltar para a última página',
    },
    pagination: { label: 'Paginação dos resultados da busca', previous: 'Página anterior', next: 'Próxima página' },
  },
  footer: {
    eyebrow: 'JS Designs',
    title: 'Papelaria afetiva com direção autoral.',
    description:
      'Loja online em construção para lembrancinhas físicas personalizadas, convites digitais personalizados e Produto Digital Pronto claramente identificados.',
    support:
      'Para contato e suporte, use a página de Suporte. O atendimento completo será conectado dentro do site em etapa própria.',
    trustCopy: 'Pagamentos protegidos e processados por parceiros certificados.',
    navAriaLabel: 'Links do rodapé',
    copyright: '© 2026 JS Designs. Loja online em implementação.',
    sections: [
      {
        id: 'loja',
        title: 'Loja',
        links: [
          { label: 'Produtos', href: '/produtos' },
          { label: 'Categorias', href: '/categorias' },
          { label: 'Buscar', href: '/buscar' },
        ],
      },
      {
        id: 'atendimento',
        title: 'Atendimento',
        links: [
          { label: 'Suporte', href: '/suporte' },
          { label: 'Entrega', href: '/entrega' },
          { label: 'Trocas e reembolso', href: '/trocas-e-reembolso' },
        ],
      },
      {
        id: 'informacoes',
        title: 'Informações',
        links: [
          { label: 'Políticas', href: '/politicas' },
          { label: 'Privacidade', href: '/privacidade' },
          { label: 'Termos', href: '/termos' },
        ],
      },
    ],
  },
  placeholders: {
    carrinho: {
      title: 'Carrinho',
      eyebrow: 'Carrinho em preparação',
      description:
        'O carrinho transacional ainda não está ativo. Nenhum item, subtotal, cupom, checkout, pagamento ou compra é simulado nesta página.',
      metadata: {
        title: 'Carrinho em preparação',
        description:
          'Carrinho da JS Designs em preparação, sem simular itens, subtotal, cupom ou checkout.',
        robots: {
          follow: false,
          index: false,
        },
      },
    },
    entrar: {
      title: 'Área da Cliente',
      eyebrow: 'Entrar ou criar conta',
      description:
        'O acesso seguro à Área da Cliente será implementado em story própria. Esta página não autentica, não cria conta e não exibe dados privados.',
      metadata: {
        title: 'Área da Cliente em preparação',
        description:
          'Área da Cliente da JS Designs em preparação, sem autenticação ou dados privados nesta etapa.',
        robots: {
          follow: false,
          index: false,
        },
      },
    },
    suporte: {
      title: 'Suporte',
      eyebrow: 'Ajuda em preparação',
      description:
        'O suporte dentro do site será conectado depois. Esta página não inicia chat nem atendimento externo.',
      metadata: {
        title: 'Suporte em preparação',
        description: 'Suporte da JS Designs em preparação para atendimento dentro do site.',
      },
    },
    politicas: {
      title: 'Políticas',
      eyebrow: 'Informações essenciais',
      description:
        'As políticas completas serão publicadas nas próximas etapas. Este placeholder preserva o caminho institucional obrigatório.',
      metadata: {
        title: 'Políticas em preparação',
        description:
          'Políticas da loja JS Designs em preparação para publicação antes do lançamento.',
      },
    },
    privacidade: {
      title: 'Privacidade',
      eyebrow: 'Proteção de dados',
      description:
        'A política de privacidade final será detalhada antes do lançamento. Este placeholder não coleta dados pessoais.',
      metadata: {
        title: 'Privacidade em preparação',
        description:
          'Política de privacidade da JS Designs em preparação, sem coleta de dados pessoais nesta página.',
      },
    },
    termos: {
      title: 'Termos',
      eyebrow: 'Condições da loja',
      description:
        'Os termos completos da JS Designs serão definidos nas próximas etapas, sem prometer regras comerciais ainda não implementadas.',
      metadata: {
        title: 'Termos em preparação',
        description:
          'Termos da loja JS Designs em preparação para publicação antes do lançamento.',
      },
    },
    entrega: {
      title: 'Entrega',
      eyebrow: 'Envio em preparação',
      description:
        'As regras de envio para produtos físicos serão implementadas no fluxo de frete. Esta página não calcula prazo nem valor.',
      metadata: {
        title: 'Entrega em preparação',
        description:
          'Informações de entrega da JS Designs em preparação, sem cálculo de prazo ou frete nesta etapa.',
      },
    },
    'trocas-e-reembolso': {
      title: 'Trocas e reembolso',
      eyebrow: 'Política em preparação',
      description:
        'As regras de troca e reembolso serão publicadas com validação operacional e legal antes do lançamento.',
      metadata: {
        title: 'Trocas e reembolso em preparação',
        description:
          'Política de trocas e reembolso da JS Designs em preparação para validação operacional e legal.',
      },
    },
  },
  placeholderFallbackCtas: {
    default: {
      href: '/produtos',
      label: 'Voltar para produtos',
    },
  },
  firstPurchaseDiscount: {
    fieldLabel: 'E-mail',
    fieldHelp:
      'Usaremos este e-mail somente para emitir e entregar o cupom solicitado. Isso não cria conta nem newsletter.',
    manualUse:
      'O código deverá ser inserido manualmente no carrinho ou checkout quando esse fluxo estiver disponível.',
    submit: 'Solicitar cupom',
    submitting: 'Solicitando...',
    close: 'Fechar oferta de desconto',
    decline: 'Agora não',
    copy: 'Copiar código',
    copied: 'Código copiado',
    confirm: 'Entendi',
    statusLabel: 'Status da solicitação do cupom',
    errors: {
      emailRequired: 'Informe seu e-mail para solicitar o cupom.',
      emailInvalid: 'Informe um e-mail válido.',
      authorizationRequired: 'Confirme a autorização específica para solicitar o cupom.',
      unavailable: 'A solicitação de cupom está temporariamente indisponível.',
      retry: 'Não foi possível concluir agora. Tente novamente em instantes.',
    },
  },
  qualityCopy: [
    'Área da Cliente',
    'Políticas',
    'Privacidade',
    'Trocas e reembolso',
    'Detalhes personalizados',
    'lembrancinhas físicas personalizadas',
    'convites digitais personalizados',
    'Produto Digital Pronto',
    'Silhouette Studio',
    'Mais procurados',
    'Kit Festa Fazendinha',
    'Convite Jardim Dourado',
    'Topo Céu Encantado',
  ],
} as const satisfies PublicContent;

export type PlaceholderPageKey = keyof typeof publicContent.placeholders;
