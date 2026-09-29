// KBC아카데미 관리자 공통 스크립트
(function () {
    // data-confirm 이 있는 폼은 제출 전에 확인창 표시
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!window.confirm(form.dataset.confirm)) e.preventDefault();
        });
    });

    // data-confirm 이 있는 버튼(목록의 삭제 등)은 누르기 전에 확인창 표시
    document.querySelectorAll('button[data-confirm]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!window.confirm(btn.dataset.confirm)) e.preventDefault();
        });
    });
})();
