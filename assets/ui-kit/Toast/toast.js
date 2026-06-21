// ================================================================
// TOAST SYSTEM
// ================================================================
var uiToastContainer = null;

function uiToastEnsureContainer() {
  if (!uiToastContainer) {
    uiToastContainer = document.getElementById('ui-toast-container');
    if (!uiToastContainer) {
      uiToastContainer = document.createElement('div');
      uiToastContainer.id = 'ui-toast-container';
      uiToastContainer.className = 'ui-toast-container';
      document.body.appendChild(uiToastContainer);
    }
  }
  return uiToastContainer;
}

function uiToast(options) {
  var title = options.title || '';
  var message = options.message || '';
  var variant = options.variant || 'info';
  var closable = options.closable !== false;
  var duration = options.duration !== undefined ? options.duration : 5000;
  var id = options.id || ('toast-' + Math.random().toString(36).substring(2, 10));

  var icons = {
    success: 'fa-check-circle',
    error: 'fa-times-circle',
    warning: 'fa-exclamation-triangle',
    info: 'fa-info-circle'
  };
  var iconClass = icons[variant] || icons.info;

  var container = uiToastEnsureContainer();

  var el = document.createElement('div');
  el.id = id;
  el.className = 'ui-toast ui-toast--' + variant;
  el.setAttribute('role', 'alert');

  var html = '<div class="ui-toast__icon"><i class="fas ' + iconClass + '"></i></div>';
  html += '<div class="ui-toast__body">';
  html += '<div class="ui-toast__title">' + title + '</div>';
  if (message) {
    html += '<div class="ui-toast__message">' + message + '</div>';
  }
  html += '</div>';

  if (closable) {
    html += '<button type="button" class="ui-toast__close" aria-label="Close">&times;</button>';
  }

  if (duration > 0) {
    html += '<div class="ui-toast__progress" style="width:100%"></div>';
  }

  el.innerHTML = html;

  if (closable) {
    el.querySelector('.ui-toast__close').addEventListener('click', function () {
      uiToastDismiss(id);
    });
  }

  el.addEventListener('mouseenter', function () {
    if (el._timer) {
      clearTimeout(el._timer);
      el._timer = null;
    }
    var progress = el.querySelector('.ui-toast__progress');
    if (progress) {
      var computed = getComputedStyle(progress);
      progress.style.transitionDuration = '0s';
      progress.style.width = computed.width;
    }
  });

  el.addEventListener('mouseleave', function () {
    if (duration > 0) {
      var progress = el.querySelector('.ui-toast__progress');
      if (progress) {
        var currentWidth = parseFloat(getComputedStyle(progress).width);
        var totalWidth = el.offsetWidth;
        var remainingRatio = totalWidth > 0 ? currentWidth / totalWidth : 0;
        var remainingTime = duration * remainingRatio;
        progress.style.transitionDuration = remainingTime + 'ms';
        progress.style.width = '0%';
        el._timer = setTimeout(function () {
          uiToastDismiss(id);
        }, remainingTime);
      }
    }
  });

  container.appendChild(el);

  if (duration > 0) {
    requestAnimationFrame(function () {
      var progress = el.querySelector('.ui-toast__progress');
      if (progress) {
        progress.style.transitionDuration = duration + 'ms';
        progress.style.width = '0%';
      }
    });
    el._timer = setTimeout(function () {
      uiToastDismiss(id);
    }, duration);
  }

  return id;
}

function uiToastDismiss(id) {
  var el = document.getElementById(id);
  if (!el || el.classList.contains('ui-toast--exiting')) return;

  if (el._timer) {
    clearTimeout(el._timer);
    el._timer = null;
  }

  el.classList.add('ui-toast--exiting');
  el.addEventListener('animationend', function () {
    el.remove();
  });
}

function uiToastSuccess(title, message, opts) {
  return uiToast(Object.assign({ title: title, message: message, variant: 'success' }, opts || {}));
}

function uiToastError(title, message, opts) {
  return uiToast(Object.assign({ title: title, message: message, variant: 'error' }, opts || {}));
}

function uiToastWarning(title, message, opts) {
  return uiToast(Object.assign({ title: title, message: message, variant: 'warning' }, opts || {}));
}

function uiToastInfo(title, message, opts) {
  return uiToast(Object.assign({ title: title, message: message, variant: 'info' }, opts || {}));
}
