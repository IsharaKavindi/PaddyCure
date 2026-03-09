<footer>
  <div class="f-bottom">
    <div class="f-bottom-inner">
      <span class="f-copy">© <?= date('Y') ?> PaddyCare. Built for Sri Lankan farmers.</span>
      <div class="f-bottom-links">
        <a href="<?= BASE_URL ?>/pages/privacy_policy.php">Privacy Policy</a>
        <a href="<?= BASE_URL ?>/pages/terms_of_service.php">Terms & Services</a>
        <a href="<?= BASE_URL ?>/pages/contact/contact.php">Contact Us</a>
      </div>
    </div>
  </div>
</footer>

<style>
footer {
  position: relative;
  z-index: 1;
  background: linear-gradient(160deg, #0d2e18 0%, #0a2214 50%, #061a0e 100%);
  border-top: 1px solid rgba(100,190,90,.25);
  margin-top: auto;
}

.f-bottom {
  background: rgba(0,0,0,.2);
}
.f-bottom-inner {
  max-width: 1180px;
  margin: 0 auto;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}
.f-copy {
  font-size: 12px;
  color: rgba(195,228,185,.4);
  font-family: 'Outfit', sans-serif;
}
.f-bottom-links {
  display: flex;
  gap: 18px;
}
.f-bottom-links a {
  font-size: 12px;
  color: rgba(195,228,185,.4);
  text-decoration: none;
  font-family: 'Outfit', sans-serif;
  transition: color .18s;
}
.f-bottom-links a:hover { color: #96D35E; }

@media (max-width: 560px) {
  .f-bottom-inner { flex-direction: column; align-items: flex-start; gap: 10px; }
}
</style>