/**
 * featured-widget.js
 * Standalone React TiltedCard + FeaturedPet widget.
 * No JSX, no Babel, no Vite — runs directly in the browser via React CDN.
 */
(function () {
  const { createElement: h, useRef, useState } = React;
  const root = ReactDOM.createRoot(document.getElementById('featured-root'));

  /* ── Inline styles (replaces TiltedCard.css) ─────────────────── */
  const style = document.createElement('style');
  style.textContent = `
    .featured-wrap {
      display: flex; flex-direction: column;
      align-items: center; gap: 16px;
    }
    .featured-label {
      background: rgba(255,92,157,0.12);
      border: 1px solid rgba(255,92,157,0.35);
      color: #e91e63; font-weight: 800;
      font-size: 13px; padding: 6px 18px;
      border-radius: 50px;
      backdrop-filter: blur(8px);
      letter-spacing: 1px;
    }
    .tilted-wrap {
      display: flex; align-items: center; justify-content: center;
    }
    .tilted-inner {
      position: relative; border-radius: 20px;
      overflow: hidden;
      transition: transform 0.15s ease;
      cursor: pointer;
      box-shadow: 0 20px 60px rgba(255,92,157,0.22), 0 4px 20px rgba(255,92,157,0.1);
      border: 1px solid rgba(255,92,157,0.12);
    }
    .tilted-inner img {
      width: 100%; height: 100%;
      object-fit: cover; display: block;
    }
    .tilted-overlay {
      position: absolute; inset: 0;
      background: linear-gradient(to top, rgba(0,0,0,0.6) 0%, transparent 60%);
      display: flex; align-items: flex-end; padding: 20px;
    }
    .tilted-overlay-content { display: flex; flex-direction: column; gap: 4px; }
    .featured-name  { color: white; font-weight: 900; font-size: 20px; text-shadow: 0 2px 10px rgba(0,0,0,0.5); }
    .featured-breed { color: rgba(255,255,255,0.75); font-size: 13px; font-weight: 600; }
    .tilted-tooltip {
      position: absolute; top: 12px; left: 12px;
      background: rgba(255,255,255,0.95);
      backdrop-filter: blur(8px);
      padding: 6px 14px; border-radius: 20px;
      font-size: 13px; font-weight: 800; color: #ff5c9d;
      font-family: 'Nunito', sans-serif;
      box-shadow: 0 2px 10px rgba(255,92,157,0.15);
    }
    .btn-adopt {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 13px 32px;
      background: linear-gradient(135deg, #ff5c9d, #e91e63);
      color: white; border-radius: 50px;
      text-decoration: none; font-weight: 800;
      font-size: 14px; transition: all 0.3s;
      box-shadow: 0 6px 20px rgba(255,92,157,0.4);
      letter-spacing: 0.5px; font-family: 'Nunito', sans-serif;
    }
    .btn-adopt:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 30px rgba(255,92,157,0.55);
      color: white;
    }
    .skeleton-card {
      width: 300px; height: 340px; border-radius: 20px;
      background: rgba(255,255,255,0.7);
      border: 1px solid rgba(255,92,157,0.15);
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      gap: 12px; color: #c07090;
      font-size: 14px; font-family: 'Nunito', sans-serif;
      box-shadow: 0 8px 30px rgba(255,92,157,0.1);
    }
  `;
  document.head.appendChild(style);

  /* ── TiltedCard component ─────────────────────────────────────── */
  function TiltedCard({ imageSrc, altText, captionText, overlayContent,
    containerHeight = '340px', containerWidth = '300px',
    imageHeight = '340px', imageWidth = '300px',
    rotateAmplitude = 10, scaleOnHover = 1.05 }) {

    const ref = useRef(null);
    const [tilt, setTilt]       = useState({ x: 0, y: 0 });
    const [hovered, setHovered] = useState(false);

    function onMove(e) {
      const rect = ref.current.getBoundingClientRect();
      const rx = ((e.clientY - rect.top  - rect.height / 2) / (rect.height / 2)) * -rotateAmplitude;
      const ry = ((e.clientX - rect.left - rect.width  / 2) / (rect.width  / 2)) *  rotateAmplitude;
      setTilt({ x: rx, y: ry });
    }

    return h('div', { className: 'tilted-wrap', style: { width: containerWidth, height: containerHeight } },
      h('div', {
        ref,
        className: 'tilted-inner',
        style: {
          width: imageWidth, height: imageHeight,
          transform: `perspective(800px) rotateX(${tilt.x}deg) rotateY(${tilt.y}deg) scale(${hovered ? scaleOnHover : 1})`
        },
        onMouseMove:  onMove,
        onMouseEnter: () => setHovered(true),
        onMouseLeave: () => { setHovered(false); setTilt({ x: 0, y: 0 }); }
      },
        h('img', { src: imageSrc, alt: altText }),
        overlayContent && h('div', { className: 'tilted-overlay' }, overlayContent),
        hovered && captionText && h('div', { className: 'tilted-tooltip' }, captionText)
      )
    );
  }

  /* ── FeaturedPet component ────────────────────────────────────── */
  function FeaturedPet() {
    const pet     = window.FEATURED_PET;
    const adoptUrl = window.ADOPT_URL;

    if (!pet) return h('div', { className: 'featured-wrap' },
      h('div', { className: 'featured-label' },
        h('i', { className: 'fa-solid fa-star', style: { marginRight: '6px' } }), 'Pet of the Day'
      ),
      h('div', { className: 'skeleton-card' }, 'No featured pet available')
    );

    return h('div', { className: 'featured-wrap' },
      h('div', { className: 'featured-label' },
        h('i', { className: 'fa-solid fa-star', style: { marginRight: '6px' } }), 'Pet of the Day'
      ),
      h(TiltedCard, {
        imageSrc:            pet.image_url,
        altText:             pet.name,
        captionText:         `${pet.name} • ${pet.breed}`,
        rotateAmplitude:     10,
        scaleOnHover:        1.05,
        overlayContent: h('div', { className: 'tilted-overlay-content' },
          h('span', { className: 'featured-name' },  pet.name),
          h('span', { className: 'featured-breed' }, `${pet.breed} • ${pet.type}`)
        )
      }),
      adoptUrl && h('a', { href: adoptUrl, className: 'btn-adopt' },
        h('i', { className: 'fa-solid fa-heart', style: { marginRight: '8px' } }),
        `Adopt ${pet.name}`
      )
    );
  }

  /* ── Mount ────────────────────────────────────────────────────── */
  root.render(h(FeaturedPet, null));
})();
