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
    var dialog  = document.getElementById('confirmDialog');
    var useDialog = dialog && typeof dialog.showModal === 'function';
    var pending = null;   // 확인 후 진행할 [폼, 누른 버튼]

    function ask(message, form, submitter) {
        if (!useDialog) {   // 확인 창을 지원하지 않는 브라우저는 기본 확인창
            if (window.confirm(message)) go(form, submitter);
            return;
        }
        pending = [form, submitter];
        document.getElementById('confirmText').textContent = message;
        dialog.showModal();
        dialog.querySelector('[data-confirm-cancel]').focus();
    }

    function go(form, submitter) {
        form.dataset.confirmed = '1';
        if (submitter && typeof form.requestSubmit === 'function') {
            form.requestSubmit(submitter);   // 누른 버튼의 formaction 을 그대로 사용
        } else {
            form.submit();
        }
    }

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (form.dataset.confirmed === '1') {
            delete form.dataset.confirmed;
            return;
        }
        var btn     = e.submitter && e.submitter.dataset.confirm !== undefined ? e.submitter : null;
        var message = btn ? btn.dataset.confirm : form.dataset.confirm;
        if (message === undefined) return;

        e.preventDefault();
        ask(message, form, e.submitter || null);
    });

    if (useDialog) {
        dialog.querySelector('[data-confirm-cancel]').addEventListener('click', function () {
            pending = null;
            dialog.close();
        });
        dialog.querySelector('[data-confirm-ok]').addEventListener('click', function () {
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
