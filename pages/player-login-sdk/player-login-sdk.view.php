<style>
.sdk-login-page {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  height: 100vh;
  padding: 40px 20px;
  box-sizing: border-box;
  background:
    radial-gradient(ellipse at 20% 50%, rgba(88, 101, 242, 0.08) 0%, transparent 50%),
    radial-gradient(ellipse at 80% 20%, rgba(129, 140, 248, 0.06) 0%, transparent 50%),
    radial-gradient(ellipse at 50% 80%, rgba(99, 102, 241, 0.05) 0%, transparent 50%),
    var(--bg-color, #f7f7f7);
  position: relative;
  overflow: hidden;
}
.sdk-login-page::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: repeating-conic-gradient(
    from 0deg at 50% 50%,
    transparent 0deg 88deg,
    rgba(88, 101, 242, 0.015) 88deg 90deg
  );
  animation: sdk-rotate-slow 120s linear infinite;
  pointer-events: none;
}
@keyframes sdk-rotate-slow {
  to { transform: rotate(360deg); }
}
.sdk-login-card {
  background: var(--bg-color-card, #fff);
  border: 1px solid var(--border-color, #e5e7eb);
  border-radius: 20px;
  padding: 64px 40px 24px;
  text-align: center;
  max-width: 500px;
  width: 100%;
  box-sizing: border-box;
  box-shadow:
    0 1px 3px rgba(0,0,0,0.04),
    0 8px 32px rgba(0,0,0,0.06);
  position: relative;
  z-index: 1;
  animation: sdk-card-in 0.5s ease-out;
}
@keyframes sdk-card-in {
  from { opacity: 0; transform: translateY(24px) scale(0.97); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}
.sdk-success-ring {
  width: 88px;
  height: 88px;
  margin: 0 auto 28px;
  border-radius: 50%;
  background: rgba(16, 185, 129, 0.08);
  display: flex;
  align-items: center;
  justify-content: center;
  animation: sdk-success-pulse 2s ease-in-out infinite;
}
@keyframes sdk-success-pulse {
  0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.15); }
  50%      { box-shadow: 0 0 0 16px rgba(16, 185, 129, 0); }
}
.sdk-success-ring .sdk-success-icon {
  font-size: 2.8em;
  color: #10b981;
  animation: sdk-pop-in 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}
@keyframes sdk-pop-in {
  from { transform: scale(0) rotate(-10deg); }
  to   { transform: scale(1) rotate(0deg); }
}
.sdk-login-card h2 {
  font-size: 1.5rem;
  font-weight: 700;
  margin: 0 0 10px;
  color: var(--text-color-headings, #333);
  letter-spacing: -0.01em;
}
.sdk-login-card .sdk-login-subtitle {
  color: var(--text-color-secondary, #666);
  margin: 0 0 36px;
  font-size: 0.92em;
  line-height: 1.6;
}
.sdk-success-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 24px;
  background: rgba(16, 185, 129, 0.08);
  border: 1px solid rgba(16, 185, 129, 0.15);
  border-radius: 100px;
  font-size: 0.85em;
  font-weight: 600;
  color: #059669;
}
.sdk-login-footer {
  margin-top: 32px;
  font-size: 0.78em;
  color: var(--text-color-secondary, #999);
}
.sdk-login-brand {
  position: fixed;
  top: 28px;
  left: 32px;
  z-index: 10;
}
.sdk-login-brand img {
  height: 40px;
  opacity: 0.85;
  transition: opacity 0.2s;
}
.sdk-login-brand img:hover {
  opacity: 1;
}
</style>

<div class="sdk-login-brand">
  <img src="/assets/images/logo<?= $theme === 'dark' ? 'White' : '' ?>.svg" alt="Platform Logo">
</div>

<div class="sdk-login-page">
  <div class="sdk-login-card">
    <div class="sdk-success-ring">
      <i class="fas fa-check sdk-success-icon"></i>
    </div>
    <h2><?= __('player_sdk_login_success') ?></h2>
    <p class="sdk-login-subtitle"><?= __('player_sdk_login_success_desc') ?></p>
  </div>
</div>
