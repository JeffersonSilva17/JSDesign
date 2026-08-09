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
        'Use Produtos, Categorias ou Buscar para continuar a descoberta. Essas rotas permanecem honestamente em preparação até receberem catálogo e busca reais.',
    },
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
    produtos: {
      title: 'Produtos',
      eyebrow: 'Catálogo em preparação',
      description:
        'A listagem real de produtos será conectada nas próximas stories. Esta página preserva a navegação pública sem simular catálogo, preço, disponibilidade, carrinho ou compra.',
      metadata: {
        title: 'Produtos em preparação',
        description:
          'Entrada pública do catálogo JS Designs em preparação, sem simular preço ou disponibilidade.',
      },
    },
    categorias: {
      title: 'Categorias',
      eyebrow: 'Descoberta em preparação',
      description:
        'As categorias de lembrancinhas físicas personalizadas, convites digitais personalizados e Produto Digital Pronto serão organizadas sem antecipar filtros ou catálogo real nesta etapa.',
      metadata: {
        title: 'Categorias em preparação',
        description:
          'Categorias públicas da JS Designs em preparação para lembrancinhas, convites e produtos digitais.',
      },
    },
    buscar: {
      title: 'Buscar',
      eyebrow: 'Busca em preparação',
      description:
        'A busca por tema, produto ou ocasião será implementada depois. Por enquanto, este espaço confirma o caminho público de descoberta.',
      metadata: {
        title: 'Busca em preparação',
        description: 'Busca pública da JS Designs em preparação para temas, produtos e ocasiões.',
      },
    },
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
    produtos: {
      href: '/',
      label: 'Voltar para a home',
    },
    default: {
      href: '/produtos',
      label: 'Voltar para produtos',
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
