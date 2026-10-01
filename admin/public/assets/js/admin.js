// KBC아카데미 관리자 공통 스크립트
(function () {
    // ── 휴대폰·태블릿: 왼쪽 메뉴 열기·닫기 ──
    var body = document.body;
    document.querySelectorAll('[data-sidebar]').forEach(function (el) {
        el.addEventListener('click', function () {
            body.classList.toggle('side-open', el.dataset.sidebar === 'open');
        });
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') body.classList.remove('side-open');
    });

    // ── 확인 창 ──
    // data-confirm 이 있는 폼(제출 시) / 버튼(누를 때)은 확인을 받은 뒤에만 진행한다.
    //   data-confirm-ok="삭제"        확인 버튼 글자 (기본: 확인)
    //   data-confirm-tone="primary"   확인 버튼 색 (기본: 빨강, primary: 남색)
    //   data-confirm-info             안내만 하고 아무것도 보내지 않음 (확인 버튼 하나)
    var dialog  = document.getElementById('confirmDialog');
    var useDialog = dialog && typeof dialog.showModal === 'function';
    var pending = null;   // 확인 후 진행할 [폼, 누른 버튼]

    function ask(el, form, submitter) {
        var message = el.dataset.confirm;
        var info    = el.dataset.confirmInfo !== undefined;

        if (!useDialog) {   // 확인 창을 지원하지 않는 브라우저는 기본 확인창
            if (info) window.alert(message);
            else if (window.confirm(message)) go(form, submitter);
            return;
        }
        var ok     = dialog.querySelector('[data-dialog-ok]');
        var cancel = dialog.querySelector('[data-dialog-cancel]');
        ok.textContent = el.dataset.confirmOk || '확인';
        ok.className   = 'btn ' + (el.dataset.confirmTone === 'primary' ? '' : 'btn-danger');
        cancel.hidden  = info;

        pending = info ? null : [form, submitter];
        document.getElementById('confirmText').textContent = message;
        dialog.showModal();
        (info ? ok : cancel).focus();
    }

    function go(form, submitter) {
        form.dataset.confirmed = '1';
        if (submitter && typeof form.requestSubmit === 'function') {
            form.requestSubmit(submitter);   // 누른 버튼의 formaction 을 그대로 사용
            return;
        }
        if (submitter) {   // requestSubmit 이 없는 옛 브라우저: 버튼의 주소·값을 직접 옮긴다
            if (submitter.hasAttribute('formaction')) form.action = submitter.formAction;
            if (submitter.name) {
                var input = document.createElement('input');
                input.type = 'hidden'; input.name = submitter.name; input.value = submitter.value;
                form.appendChild(input);
            }
        }
        form.submit();
    }

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (form.dataset.confirmed === '1') {
            delete form.dataset.confirmed;
            return;
        }
        var btn = e.submitter && e.submitter.dataset.confirm !== undefined ? e.submitter : null;
        var el  = btn || (form.dataset.confirm !== undefined ? form : null);
        if (!el) return;

        e.preventDefault();
        ask(el, form, e.submitter || null);
    });

    if (useDialog) {
        dialog.querySelector('[data-dialog-cancel]').addEventListener('click', function () {
            pending = null;
            dialog.close();
        });
        dialog.querySelector('[data-dialog-ok]').addEventListener('click', function () {
            var p = pending;
            pending = null;
            dialog.close();
            if (p) go(p[0], p[1]);
        });
        dialog.addEventListener('close', function () { pending = null; });
        // 창 바깥(어두운 부분)을 누르면 취소
        dialog.addEventListener('click', function (e) {
            if (e.target === dialog) dialog.close();
        });
    }
})();
