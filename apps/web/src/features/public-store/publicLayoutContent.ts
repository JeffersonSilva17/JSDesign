export const mainNavItems = [
  { label: 'Home', href: '/', description: 'Voltar para a página inicial da JS Designs.' },
  { label: 'Produtos', href: '/produtos', description: 'Ver a entrada futura para produtos da loja.' },
  { label: 'Categorias', href: '/categorias', description: 'Ver categorias principais da loja.' },
  { label: 'Buscar', href: '/buscar', description: 'Buscar por tema, produto ou ocasião.' },
  { label: 'Carrinho', href: '/carrinho', description: 'Revisar itens escolhidos futuramente.' },
  { label: 'Área da Cliente', href: '/entrar', description: 'Entrar ou criar acesso à Área da Cliente.' },
  { label: 'Suporte', href: '/suporte', description: 'Acessar ajuda dentro do site.' },
] as const;

export const publicCta = {
  label: 'Ver produtos',
  href: '/produtos',
  description: 'Começar a descoberta de produtos JS Designs.',
} as const;

export const footerSections = [
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
] as const;

export const placeholderPages = {
  produtos: {
    title: 'Produtos',
    eyebrow: 'Catálogo em preparação',
    description:
      'A listagem real de produtos será conectada nas próximas stories. Esta página preserva a navegação pública sem simular catálogo, preço ou disponibilidade.',
  },
  categorias: {
    title: 'Categorias',
    eyebrow: 'Descoberta em preparação',
    description:
      'As categorias de lembrancinhas, convites e produtos digitais serão organizadas sem antecipar filtros ou catálogo real nesta etapa.',
  },
  buscar: {
    title: 'Buscar',
    eyebrow: 'Busca em preparação',
    description:
      'A busca por tema, produto ou ocasião será implementada depois. Por enquanto, este espaço confirma o caminho público de descoberta.',
  },
  carrinho: {
    title: 'Carrinho',
    eyebrow: 'Carrinho em preparação',
    description:
      'O carrinho transacional ainda não está ativo. Nenhum item, subtotal, cupom ou checkout é simulado nesta página.',
  },
  entrar: {
    title: 'Área da Cliente',
    eyebrow: 'Entrar ou criar conta',
    description:
      'O acesso seguro à Área da Cliente será implementado em story própria. Esta página não autentica, não cria conta e não exibe dados privados.',
  },
  suporte: {
    title: 'Suporte',
    eyebrow: 'Ajuda em preparação',
    description:
      'O suporte dentro do site será conectado depois. Esta página não inicia chat nem atendimento externo.',
  },
  politicas: {
    title: 'Políticas',
    eyebrow: 'Informações essenciais',
    description:
      'As políticas completas serão publicadas nas próximas etapas. Este placeholder preserva o caminho institucional obrigatório.',
  },
  privacidade: {
    title: 'Privacidade',
    eyebrow: 'Proteção de dados',
    description:
      'A política de privacidade final será detalhada antes do lançamento. Este placeholder não coleta dados pessoais.',
  },
  termos: {
    title: 'Termos',
    eyebrow: 'Condições da loja',
    description:
      'Os termos completos da JS Designs serão definidos nas próximas etapas, sem prometer regras comerciais ainda não implementadas.',
  },
  entrega: {
    title: 'Entrega',
    eyebrow: 'Envio em preparação',
    description:
      'As regras de envio para produtos físicos serão implementadas no fluxo de frete. Esta página não calcula prazo nem valor.',
  },
  'trocas-e-reembolso': {
    title: 'Trocas e reembolso',
    eyebrow: 'Política em preparação',
    description:
      'As regras de troca e reembolso serão publicadas com validação operacional e legal antes do lançamento.',
  },
} as const;

export type PlaceholderPageKey = keyof typeof placeholderPages;
