<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ duration: 600, once: true, easing: 'ease-out-cubic' });

  // Floating paw prints
  const container = document.getElementById('pawsBg');
  if (container) {
    for (let i = 0; i < 14; i++) {
      const el = document.createElement('span');
      el.className = 'paw';
      el.innerHTML = '<i class="fa-solid fa-paw"></i>';
      el.style.left              = Math.random() * 100 + 'vw';
      el.style.animationDuration = (12 + Math.random() * 15) + 's';
      el.style.animationDelay    = (Math.random() * 15) + 's';
      el.style.fontSize          = (16 + Math.random() * 18) + 'px';
      container.appendChild(el);
    }
  }
</script>
