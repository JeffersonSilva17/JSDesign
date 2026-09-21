import { ImageResponse } from 'next/og';

import { catalogSeoContent } from '@/i18n/catalogSeoContent';

// Editorial content only: safe to prerender once, independent of catalog publication.
export const dynamic = 'force-static';

export function GET() {
  return new ImageResponse(
    <div style={{ display: 'flex', flexDirection: 'column', justifyContent: 'center', alignItems: 'center', width: '100%', height: '100%', background: '#f8f5f1', color: '#2d2d2d', border: '24px solid #c8a46b', fontFamily: 'sans-serif' }}>
      <div style={{ fontSize: 110, color: '#765321' }}>{catalogSeoContent.socialTitle}</div>
      <div style={{ fontSize: 36, marginTop: 35 }}>{catalogSeoContent.socialDescription}</div>
    </div>,
    { width: 1200, height: 630 },
  );
}
