// KBC아카데미 관리자 공통 스크립트
(function () {
    // data-confirm 이 있는 폼은 제출 전에 확인창 표시
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!window.confirm(form.dataset.confirm)) e.preventDefault();
        });
    });
})();
