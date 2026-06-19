<?php

function ui_file_upload($name, $options = []) {
  $label = $options['label'] ?? '';
  $accept = $options['accept'] ?? '.csv';
  $required = $options['required'] ?? false;
  $disabled = $options['disabled'] ?? false;
  $class = $options['class'] ?? '';
  $hint = $options['hint'] ?? '';
  $error = $options['error'] ?? '';
  $id = $options['id'] ?? $name;
  $placeholder = $options['placeholder'] ?? 'Clicca per selezionare il file';
  $formats = $options['formats'] ?? '';
  $info = $options['info'] ?? '';
  $maxSize = $options['max_size'] ?? '';

  $requiredMark = $required ? ' after:content-["_*"] after:text-red-600' : '';
  $disabledAttr = $disabled ? ' disabled' : '';
  $idAttr = htmlspecialchars($id);
  $nameAttr = htmlspecialchars($name);

  $html = '<div class="mb-4">';

  if ($label) {
    $html .= '<label class="block font-semibold mb-1.5 text-sm text-[var(--text-color)]' . $requiredMark . '" for="' . $idAttr . '-input">' . htmlspecialchars($label) . '</label>';
  }

  $dropzoneClass = 'border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition-colors duration-200';
  if ($error) {
    $dropzoneClass .= ' border-red-600 hover:border-red-500';
  } else {
    $dropzoneClass .= ' border-[var(--border-color)] hover:border-[var(--primary-color)] hover:bg-blue-50 dark:hover:bg-blue-900/20';
  }
  if ($disabled) {
    $dropzoneClass .= ' opacity-50 cursor-not-allowed';
  }

  $html .= '<div id="' . $idAttr . '-dropzone" class="' . $dropzoneClass . '" data-file-upload="' . $idAttr . '">';
  $html .= '<i class="fas fa-cloud-upload-alt text-3xl text-[var(--text-color-secondary)] mb-3"></i>';
  $html .= '<p class="text-[var(--text-color)] font-medium">' . htmlspecialchars($placeholder) . '</p>';

  $metaParts = [];
  if ($formats) $metaParts[] = htmlspecialchars($formats);
  if ($maxSize) $metaParts[] = htmlspecialchars($maxSize);
  if (count($metaParts) > 0) {
    $html .= '<p class="text-xs text-[var(--text-color-secondary)] mt-1">' . implode(' · ', $metaParts) . '</p>';
  }

  $html .= '</div>';

  $html .= '<input type="file" id="' . $idAttr . '-input" name="' . $nameAttr . '" accept="' . htmlspecialchars($accept) . '" style="display:none"' . $disabledAttr . '>';

  $html .= '<div id="' . $idAttr . '-file-info" style="display:none" class="flex items-center gap-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg mt-2">';
  $html .= '<i class="fas fa-file text-blue-600 text-xl"></i>';
  $html .= '<div class="flex-1 min-w-0">';
  $html .= '<p id="' . $idAttr . '-file-name" class="font-medium text-[var(--text-color)] truncate"></p>';
  $html .= '<p id="' . $idAttr . '-file-size" class="text-xs text-[var(--text-color-secondary)]"></p>';
  $html .= '</div>';
  $html .= '<button type="button" class="text-[var(--text-color-secondary)] hover:text-red-600 flex-shrink-0" onclick="uiFileUploadClear(\'' . $idAttr . '\')">';
  $html .= '<i class="fas fa-times"></i>';
  $html .= '</button>';
  $html .= '</div>';

  if ($info) {
    $html .= '<div class="text-xs text-[var(--text-color-secondary)] mt-2 flex items-start gap-1.5"><i class="fas fa-info-circle mt-0.5 flex-shrink-0"></i><span>' . $info . '</span></div>';
  }

  if ($hint) {
    $html .= '<div class="text-xs text-[var(--text-color-secondary)] mt-1">' . htmlspecialchars($hint) . '</div>';
  }
  if ($error) {
    $html .= '<div class="text-xs text-red-600 mt-1">' . htmlspecialchars($error) . '</div>';
  }

  $html .= '</div>';

  return $html;
}

function ui_file_upload_js() {
  static $loaded = false;
  if ($loaded) return '';
  $loaded = true;
  return '<script>
document.addEventListener("DOMContentLoaded", function() {
  document.querySelectorAll("[data-file-upload]").forEach(function(dropzone) {
    var id = dropzone.getAttribute("data-file-upload");
    var input = document.getElementById(id + "-input");
    if (!input) return;

    ["dragenter", "dragover"].forEach(function(evt) {
      dropzone.addEventListener(evt, function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.add("border-blue-500", "bg-blue-50");
      });
    });

    ["dragleave", "drop"].forEach(function(evt) {
      dropzone.addEventListener(evt, function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.remove("border-blue-500", "bg-blue-50");
      });
    });

    dropzone.addEventListener("drop", function(e) {
      var files = e.dataTransfer.files;
      if (files && files[0]) {
        var dt = new DataTransfer();
        dt.items.add(files[0]);
        input.files = dt.files;
        uiFileUploadShowInfo(id, files[0]);
        input.dispatchEvent(new Event("change"));
      }
    });

    dropzone.addEventListener("click", function() {
      input.click();
    });

    input.addEventListener("change", function() {
      if (input.files && input.files[0]) {
        uiFileUploadShowInfo(id, input.files[0]);
      }
    });
  });
});

function uiFileUploadShowInfo(id, file) {
  document.getElementById(id + "-dropzone").style.display = "none";
  var info = document.getElementById(id + "-file-info");
  info.style.display = "flex";
  document.getElementById(id + "-file-name").textContent = file.name;
  document.getElementById(id + "-file-size").textContent = uiFileUploadFormatSize(file.size);
}

function uiFileUploadClear(id) {
  var input = document.getElementById(id + "-input");
  input.value = "";
  document.getElementById(id + "-file-info").style.display = "none";
  document.getElementById(id + "-dropzone").style.display = "block";
  input.dispatchEvent(new Event("change"));
}

function uiFileUploadFormatSize(bytes) {
  if (bytes < 1024) return bytes + " B";
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + " KB";
  return (bytes / (1024 * 1024)).toFixed(1) + " MB";
}
</script>';
}
