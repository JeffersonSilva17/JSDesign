import { serializeJsonLd } from './catalogStructuredData';

export function StructuredData({ value }: Readonly<{ value: Parameters<typeof serializeJsonLd>[0] }>) {
  // JSON-only serializer escapes every '<'; unit and browser tests cover closing-script injection.
  // nosemgrep: typescript.react.security.audit.react-dangerouslysetinnerhtml.react-dangerouslysetinnerhtml
  return <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: serializeJsonLd(value) }} />;
}
