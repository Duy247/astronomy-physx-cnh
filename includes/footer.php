<?php // Site footer ?>
<footer class="astro-footer" style="background: var(--header-bg); color: var(--header-text); padding: 2.2rem 0 1.2rem 0; margin-top: 3rem; border-radius: 0 0 0 0; box-shadow: 0 -2px 12px rgba(0,0,0,0.10); font-family: 'Montserrat', Arial, Helvetica, sans-serif; font-size: 1.08rem;">
  <div class="footer-desktop" style="max-width:1100px;margin:0 auto;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:2.5rem 1.5rem;">
    <div style="display:flex;flex-direction:column;align-items:flex-start;min-width:220px;">
      <div style="display:flex;align-items:center;gap:1.1em;">
        <img src="https://www.physx-cnh.com/image/logo.png" alt="Logo" style="height:40%;width:40%;object-fit:contain;display:block;">
        <span style="font-size:1.25rem; font-weight:600; letter-spacing:0.04em; color:var(--accent);">AstroGallery
            <div style="margin-top:0.3em;margin-left:0px;font-size:1.05rem; color:white;">Made by <span style="font-weight:600; color:var(--accent);">Duy</span></div>
        </span>
      </div>
      </div>
    <div style="text-align:right;flex:1;min-width:220px;">
      <div style="font-size:0.98rem; color:var(--header-text); opacity:0.8; margin-bottom:0.5em;">
        Data from Misti Observatory Website.<br>
        All copyright of images go to them.
      </div>
      <div style="font-size:0.95rem; color:var(--header-text); opacity:0.7;">&copy; <?php echo date('Y'); ?> AstroGallery. All rights reserved.</div>
    </div>
  </div>
    <div class="footer-mobile" style="display:none;text-align:center;">
     <div style="text-align:center;flex:1;min-width:220px;">
      <div style="font-size:0.98rem; color:var(--header-text); opacity:0.8; margin-bottom:0.5em;">
        Data from Misti Observatory Website.<br>
        All copyright of images go to them.
      </div>
      <div style="font-size:0.95rem; color:var(--header-text); opacity:0.7;">&copy; <?php echo date('Y'); ?> AstroGallery. All rights reserved.</div>
    </div>
  </div>
  <style>
    @media (max-width: 600px) {
      .footer-desktop { display: none !important; }
      .footer-mobile { display: block !important; }
    }
    @media (min-width: 601px) {
      .footer-desktop { display: flex !important; }
      .footer-mobile { display: none !important; }
    }
  </style>
</footer>
</body>
</html>
