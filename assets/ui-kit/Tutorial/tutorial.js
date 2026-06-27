(function() {
  'use strict';

  var root = document.getElementById('ui-tutorial-root');
  if (!root) return;

  var steps = JSON.parse(root.dataset.tutorialSteps || '[]');
  var currentStepId = root.dataset.tutorialCurrent || '';
  var currentPage = root.dataset.tutorialPage || '';
  var csrfToken = root.dataset.csrf || '';
  var totalSteps = parseInt(root.dataset.tutorialTotal || '0', 10);
  var startIndex = parseInt(root.dataset.tutorialIndex || '-1', 10);
  var isActive = root.dataset.tutorialActive === '1';
  var strSkip = root.dataset.tutorialSkip || 'Skip tutorial';
  var strBack = root.dataset.tutorialBack || 'Back';
  var strNext = root.dataset.tutorialNext || 'Next';
  var strFinish = root.dataset.tutorialFinish || 'Finish';
  var strGetStarted = root.dataset.tutorialGetStarted || 'Get Started';
  var strWaitingTitle = root.dataset.tutorialWaitingTitle || 'Almost there!';
  var strWaitingDesc = root.dataset.tutorialWaitingDesc || 'Create a game to continue. The tutorial will resume automatically.';
  var pagesRequiringId = ['game'];

  if (!steps.length || !isActive) return;

  var currentIndex = startIndex >= 0 ? startIndex : -1;
  var highlightEl = null;
  var overlayEl = null;
  var bubbleEl = null;

  function findStepByPage(page) {
    for (var i = 0; i < steps.length; i++) {
      if (steps[i].page === page) return i;
    }
    return -1;
  }

  function waitForElement(selector, callback, maxWait) {
    maxWait = maxWait || 8000;
    var el = document.querySelector(selector);
    if (el) { callback(el); return; }

    var waited = 0;
    var interval = setInterval(function() {
      waited += 100;
      el = document.querySelector(selector);
      if (el) {
        clearInterval(interval);
        callback(el);
      } else if (waited >= maxWait) {
        clearInterval(interval);
        callback(null);
      }
    }, 100);
  }

  function positionBubble(targetEl, step) {
    var rect = targetEl.getBoundingClientRect();
    var bubbleRect = bubbleEl.getBoundingClientRect();
    var gap = 14;
    var top, left;
    var pos = step.pos || 'bottom';

    if (pos === 'bottom') {
      top = rect.bottom + gap;
      left = rect.left + (rect.width / 2) - (bubbleRect.width / 2);
    } else if (pos === 'top') {
      top = rect.top - bubbleRect.height - gap;
      left = rect.left + (rect.width / 2) - (bubbleRect.width / 2);
    } else if (pos === 'right') {
      top = rect.top + (rect.height / 2) - (bubbleRect.height / 2);
      left = rect.right + gap;
    } else if (pos === 'left') {
      top = rect.top + (rect.height / 2) - (bubbleRect.height / 2);
      left = rect.left - bubbleRect.width - gap;
    }

    var margin = 12;
    if (left < margin) left = margin;
    if (left + bubbleRect.width > window.innerWidth - margin) {
      left = window.innerWidth - bubbleRect.width - margin;
    }
    if (top < margin) {
      top = rect.top + gap;
      pos = 'top';
    }
    if (top + bubbleRect.height > window.innerHeight - margin) {
      top = rect.top - bubbleRect.height - gap;
      pos = 'top';
    }

    bubbleEl.style.top = top + 'px';
    bubbleEl.style.left = left + 'px';

    var arrow = bubbleEl.querySelector('.ui-tutorial-bubble__arrow');
    if (arrow) {
      arrow.className = 'ui-tutorial-bubble__arrow ui-tutorial-bubble__arrow--' + pos;
      var arrowLeft = rect.left + (rect.width / 2) - left;
      arrowLeft = Math.max(16, Math.min(arrowLeft, bubbleRect.width - 16));
      arrow.style.left = arrowLeft + 'px';
      if (pos === 'bottom') {
        arrow.style.top = '-7px';
        arrow.style.bottom = '';
      } else {
        arrow.style.bottom = '-7px';
        arrow.style.top = '';
      }
    }
  }

  function renderHighlight(targetEl) {
    document.body.style.overflow = 'hidden';

    if (overlayEl) overlayEl.remove();
    overlayEl = document.createElement('div');
    overlayEl.className = 'ui-tutorial-overlay';
    document.body.appendChild(overlayEl);

    if (highlightEl) highlightEl.remove();
    highlightEl = document.createElement('div');
    highlightEl.className = 'ui-tutorial-highlight';
    var rect = targetEl.getBoundingClientRect();
    highlightEl.style.top = (rect.top - 4) + 'px';
    highlightEl.style.left = (rect.left - 4) + 'px';
    highlightEl.style.width = (rect.width + 8) + 'px';
    highlightEl.style.height = (rect.height + 8) + 'px';
    document.body.appendChild(highlightEl);
  }

  function renderBubble(step, stepIndex) {
    if (bubbleEl) bubbleEl.remove();
    bubbleEl = document.createElement('div');
    bubbleEl.className = 'ui-tutorial-bubble';

    var dots = '';
    for (var i = 0; i < totalSteps; i++) {
      var cls = 'ui-tutorial-bubble__dot';
      if (i === stepIndex) cls += ' ui-tutorial-bubble__dot--active';
      else if (i < stepIndex) cls += ' ui-tutorial-bubble__dot--done';
      dots += '<div class="' + cls + '"></div>';
    }

    var isLast = step.final || stepIndex === totalSteps - 1;
    var nextLabel = isLast ? strFinish : strNext;
    var skipLabel = isLast ? '' : '<button class="ui-tutorial-bubble__skip" id="ui-tutorial-skip">' + escapeHtml(strSkip) + '</button>';
    var arrowHtml = step.arrow !== false ? '<div class="ui-tutorial-bubble__arrow ui-tutorial-bubble__arrow--' + (step.pos || 'bottom') + '"></div>' : '';

    bubbleEl.innerHTML =
      arrowHtml +
      '<div class="ui-tutorial-bubble__progress">' + dots + '</div>' +
      '<div class="ui-tutorial-bubble__title">' + escapeHtml(step.title) + '</div>' +
      '<div class="ui-tutorial-bubble__desc">' + escapeHtml(step.desc) + '</div>' +
      '<div class="ui-tutorial-bubble__actions">' +
        skipLabel +
        '<div class="ui-tutorial-bubble__btns">' +
          (stepIndex > 0 ? '<button class="ui-tutorial-bubble__btn ui-tutorial-bubble__btn--secondary" id="ui-tutorial-back">' + escapeHtml(strBack) + '</button>' : '') +
          '<button class="ui-tutorial-bubble__btn ui-tutorial-bubble__btn--primary" id="ui-tutorial-next">' + escapeHtml(nextLabel) + '</button>' +
        '</div>' +
      '</div>';

    document.body.appendChild(bubbleEl);

    document.getElementById('ui-tutorial-next').addEventListener('click', function() {
      advanceTutorial();
    });

    var backBtn = document.getElementById('ui-tutorial-back');
    if (backBtn) {
      backBtn.addEventListener('click', function() {
        goBack();
      });
    }

    var skipBtn = document.getElementById('ui-tutorial-skip');
    if (skipBtn) {
      skipBtn.addEventListener('click', function() {
        skipTutorial();
      });
    }
  }

  function escapeHtml(str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
  }

  function showStep(stepIndex) {
    if (stepIndex < 0 || stepIndex >= steps.length) return;
    var prevIndex = currentIndex;
    currentIndex = stepIndex;
    var step = steps[stepIndex];

    if (prevIndex >= 0 && prevIndex < steps.length && steps[prevIndex].id === 'client-secret') {
      var prevInput = document.getElementById('input-secret');
      if (prevInput) prevInput.type = 'password';
    }

    if (step.final) {
      showCelebration(step);
      return;
    }

    waitForElement(step.target, function(targetEl) {
      if (!targetEl) return;

      targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });

      setTimeout(function() {
        if (step.id === 'client-secret') {
          targetEl.type = 'text';
        }
        renderHighlight(targetEl);
        renderBubble(step, stepIndex);
        positionBubble(targetEl, step);
      }, 350);
    });
  }

  function showCelebration(step) {
    if (highlightEl) highlightEl.remove();
    if (bubbleEl) bubbleEl.remove();
    if (overlayEl) overlayEl.remove();

    overlayEl = document.createElement('div');
    overlayEl.className = 'ui-tutorial-celebration';
    overlayEl.innerHTML =
      '<div class="ui-tutorial-celebration__card">' +
        '<div class="ui-tutorial-celebration__icon">🎉</div>' +
        '<div class="ui-tutorial-celebration__title">' + escapeHtml(step.title) + '</div>' +
        '<div class="ui-tutorial-celebration__desc">' + escapeHtml(step.desc) + '</div>' +
        '<button class="ui-tutorial-celebration__btn" id="ui-tutorial-finish">' + escapeHtml(strGetStarted) + '</button>' +
      '</div>';

    document.body.appendChild(overlayEl);

    document.getElementById('ui-tutorial-finish').addEventListener('click', function() {
      updateProgress('__complete__', function() {
        cleanup();
      });
    });
  }

  function advanceTutorial() {
    var nextIndex = currentIndex + 1;
    if (nextIndex >= steps.length) {
      cleanup();
      updateProgress('__complete__', function() {});
      return;
    }
    var nextStep = steps[nextIndex];
    var isDifferentPage = nextStep.page !== currentPage;
    var canNavigate = !isDifferentPage || pagesRequiringId.indexOf(nextStep.page) === -1;
    updateProgress(nextStep.id, function() {
      if (canNavigate) {
        cleanup();
        if (isDifferentPage) {
          window.location.href = '/' + nextStep.page + '.php?tutorial=' + encodeURIComponent(nextStep.id);
        } else {
          showStep(nextIndex);
        }
      } else {
        if (bubbleEl) bubbleEl.remove();
        showWaitingBubble();
      }
    });
  }

  function showWaitingBubble() {
    if (highlightEl) { highlightEl.remove(); highlightEl = null; }
    if (overlayEl) { overlayEl.remove(); overlayEl = null; }
    if (bubbleEl) bubbleEl.remove();
    document.body.style.overflow = '';
    bubbleEl = document.createElement('div');
    bubbleEl.className = 'ui-tutorial-bubble';
    bubbleEl.style.top = '16px';
    bubbleEl.style.left = '50%';
    bubbleEl.style.transform = 'translateX(-50%)';
    bubbleEl.innerHTML =
      '<div class="ui-tutorial-bubble__title">' + escapeHtml(strWaitingTitle) + '</div>' +
      '<div class="ui-tutorial-bubble__desc">' + escapeHtml(strWaitingDesc) + '</div>';
    document.body.appendChild(bubbleEl);
  }

  function goBack() {
    var prevIndex = currentIndex - 1;
    cleanup();
    if (prevIndex < 0) return;
    var prevStep = steps[prevIndex];
    var isDifferentPage = prevStep.page !== currentPage;
    var canNavigate = !isDifferentPage || pagesRequiringId.indexOf(prevStep.page) === -1;
    updateProgress(prevStep.id, function() {
      if (canNavigate) {
        cleanup();
        if (isDifferentPage) {
          window.location.href = '/' + prevStep.page + '.php?tutorial=' + encodeURIComponent(prevStep.id);
        } else {
          showStep(prevIndex);
        }
      }
    });
  }

  function skipTutorial() {
    cleanup();
    fetch('/api/tutorial-progress.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'skip', csrf_token: csrfToken })
    }).catch(function() {});
  }

  function updateProgress(stepId, callback) {
    fetch('/api/tutorial-progress.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'progress', step: stepId, csrf_token: csrfToken })
    }).then(function() {
      if (callback) callback();
    }).catch(function() {
      if (callback) callback();
    });
  }

  function cleanup() {
    if (highlightEl) { highlightEl.remove(); highlightEl = null; }
    if (bubbleEl) { bubbleEl.remove(); bubbleEl = null; }
    if (overlayEl) { overlayEl.remove(); overlayEl = null; }
    var secretInput = document.getElementById('input-secret');
    if (secretInput) secretInput.type = 'password';
    document.body.style.overflow = '';
  }

  function cleanUrlParam() {
    var params = new URLSearchParams(window.location.search);
    if (params.has('tutorial')) {
      params.delete('tutorial');
      var clean = params.toString() ? '?' + params.toString() : window.location.pathname;
      history.replaceState(null, '', clean);
    }
  }

  function init() {
    var forcedStep = null;
    var params = new URLSearchParams(window.location.search);
    var forcedStepId = params.get('tutorial');
    if (forcedStepId) {
      for (var i = 0; i < steps.length; i++) {
        if (steps[i].id === forcedStepId && steps[i].page === currentPage) {
          forcedStep = i;
          break;
        }
      }
      cleanUrlParam();
    }

    if (forcedStep !== null) {
      currentIndex = forcedStep;
      updateProgress(steps[forcedStep].id, function() {
        showStep(forcedStep);
      });
      return;
    }

    if (currentIndex >= 0 && currentIndex < steps.length) {
      var step = steps[currentIndex];
      if (step.page === currentPage) {
        showStep(currentIndex);
      }
    } else {
      var pageStep = findStepByPage(currentPage);
      if (pageStep >= 0) {
        currentIndex = pageStep;
        showStep(pageStep);
      }
    }
  }

  var readyState = document.readyState;
  if (readyState === 'complete' || readyState === 'interactive') {
    setTimeout(init, 100);
  } else {
    document.addEventListener('DOMContentLoaded', function() {
      setTimeout(init, 100);
    });
  }

  window.addEventListener('resize', function() {
    if (highlightEl && currentIndex >= 0 && currentIndex < steps.length) {
      var step = steps[currentIndex];
      var targetEl = document.querySelector(step.target);
      if (targetEl) {
        renderHighlight(targetEl);
        positionBubble(targetEl, step);
      }
    }
  });
})();
