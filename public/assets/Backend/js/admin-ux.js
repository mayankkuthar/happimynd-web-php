/*
 * Small UX helpers shared by every admin page (loaded after custom.js).
 *  - Sidebar search: type to filter the menu, matching parents stay visible.
 *  - Submit buttons show "busy" so a form is not sent twice.
 *  - Success messages fade out after a few seconds.
 *  - Any link/button with data-confirm="..." asks before continuing.
 */
(function ($) {
  'use strict';
  if (!$) { return; }

  // ---------- Sidebar search ----------
  function initMenuSearch() {
    var $menu = $('#sidebar-menu');
    var $input = $('#hm-menu-search');
    if (!$menu.length || !$input.length) { return; }

    var $empty = $('.hm-menu-empty');
    var $items = $menu.find('li');
    // remember which sub-menus were open before searching
    var openBefore = null;

    function clearMarks() {
      $menu.find('mark.hm-hit').each(function () {
        $(this).replaceWith(this.textContent);
      });
    }

    function ownText($li) {
      return $li.children('a').first().clone().children('.badge, .fa').remove().end().text().trim();
    }

    function filter(term) {
      clearMarks();
      term = term.trim().toLowerCase();

      if (!term) {
        $items.show();
        $menu.find('.menu_section').show();
        $menu.find('ul.child_menu').each(function () {
          if (openBefore && openBefore.indexOf(this) === -1) { $(this).hide(); }
        });
        openBefore = null;
        $empty.hide();
        return;
      }

      if (openBefore === null) {
        openBefore = $menu.find('ul.child_menu:visible').get();
      }

      $items.hide();
      var hits = 0;
      $items.each(function () {
        var $li = $(this);
        var text = ownText($li);
        if (text.toLowerCase().indexOf(term) === -1) { return; }
        hits++;
        // show the match, its whole sub-menu and every parent level
        $li.show().find('li').show();
        $li.parents('li').show();
        $li.parents('ul.child_menu').show();
        // highlight the matched part of the label
        var $a = $li.children('a').first();
        $a.contents().filter(function () { return this.nodeType === 3; }).each(function () {
          var t = this.textContent;
          var i = t.toLowerCase().indexOf(term);
          if (i === -1) { return; }
          var frag = document.createDocumentFragment();
          frag.appendChild(document.createTextNode(t.slice(0, i)));
          var m = document.createElement('mark');
          m.className = 'hm-hit';
          m.textContent = t.slice(i, i + term.length);
          frag.appendChild(m);
          frag.appendChild(document.createTextNode(t.slice(i + term.length)));
          this.parentNode.replaceChild(frag, this);
        });
      });

      $menu.find('.menu_section').each(function () {
        $(this).toggle($(this).find('li:visible').length > 0);
      });
      $empty.toggle(hits === 0);
    }

    $input.on('input', function () { filter(this.value); });
    $input.on('keydown', function (e) {
      if (e.key === 'Escape') { this.value = ''; filter(''); this.blur(); }
      if (e.key === 'Enter') {
        var $first = $menu.find('li:visible > a[href]').filter(function () {
          return $(this).attr('href') && $(this).attr('href') !== '#';
        }).filter(function () { return $(this).find('mark.hm-hit').length; }).first();
        if ($first.length) { window.location.href = $first.attr('href'); }
      }
    });

    // "/" focuses the menu search (when not typing in a field)
    $(document).on('keydown', function (e) {
      if (e.key !== '/' || $(e.target).is('input, textarea, select, [contenteditable]')) { return; }
      e.preventDefault();
      $input.trigger('focus');
    });
  }

  // ---------- Busy submit buttons ----------
  function initBusyButtons() {
    $(document).on('submit', 'form', function (e) {
      var form = this;
      // let validation / ajax handlers run first; they cancel the event if needed
      setTimeout(function () {
        if (e.isDefaultPrevented() || form.hasAttribute('data-no-busy') || form.target) { return; }
        var $btn = $(form).find('button[type="submit"], input[type="submit"], button:not([type])').first();
        if (!$btn.length || $btn.hasClass('hm-busy')) { return; }

        $btn.addClass('hm-busy').attr('aria-busy', 'true');
        if ($btn.is('button')) {
          $btn.data('hm-html', $btn.html());
          $btn.html('<span class="hm-spinner" aria-hidden="true"></span>' + ($btn.data('busy-text') || 'Please wait…'));
        }
        // downloads (exports) keep the page open, so give the button back
        setTimeout(function () {
          $btn.removeClass('hm-busy').removeAttr('aria-busy');
          if ($btn.data('hm-html')) { $btn.html($btn.data('hm-html')); }
        }, 8000);
      }, 0);
    });

    // back/forward cache: never show a stuck spinner
    window.addEventListener('pageshow', function () {
      $('.hm-busy').each(function () {
        var $b = $(this).removeClass('hm-busy').removeAttr('aria-busy');
        if ($b.data('hm-html')) { $b.html($b.data('hm-html')); }
      });
    });
  }

  // ---------- Fade out success messages ----------
  function initAlerts() {
    setTimeout(function () {
      $('.right_col .alert-success, .right_col .flash-message .alert-success').not('[data-keep]').fadeOut(400);
    }, 6000);
  }

  // ---------- data-confirm ----------
  function initConfirm() {
    $(document).on('click', '[data-confirm]', function (e) {
      if (!window.confirm($(this).data('confirm'))) {
        e.preventDefault();
        e.stopImmediatePropagation();
      }
    });
  }

  $(function () {
    initMenuSearch();
    initBusyButtons();
    initAlerts();
    initConfirm();
  });
})(window.jQuery);
