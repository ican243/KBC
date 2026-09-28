// KBC아카데미 공개 홈페이지 공통 스크립트
(function () {
    document.documentElement.classList.remove('no-js');

    // 첫 화면을 지나면 헤더를 흰색으로
    var header = document.querySelector('.site-header');
    var hero = document.querySelector('.hero');
    if (header && hero) {
        var onScroll = function () {
            header.classList.toggle('scrolled', window.scrollY > hero.offsetHeight - header.offsetHeight);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    // 스크롤 시 요소 등장
    var items = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) {
                    e.target.classList.add('show');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.12 });
        items.forEach(function (el) { io.observe(el); });
    } else {
        items.forEach(function (el) { el.classList.add('show'); });
    }
})();
