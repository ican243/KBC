// KBC아카데미 공개 홈페이지 공통 스크립트
(function () {
    document.documentElement.classList.remove('no-js');

    // 첫 화면을 지나면 헤더를 흰색으로
    var header = document.querySelector('.site-header');
    var hero = document.querySelector('.hero, .page-head');
    if (header && hero) {
        var onScroll = function () {
            header.classList.toggle('scrolled', window.scrollY > hero.offsetHeight - header.offsetHeight);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    // 유입 경로: 처음 들어온 경로(광고 utm 값 또는 외부 사이트 주소)를 기억해 문의 폼에 담는다
    try {
        var params = new URLSearchParams(location.search);
        var found = null;
        if (params.get('utm_source')) {
            found = 'utm:' + [params.get('utm_source'), params.get('utm_medium'), params.get('utm_campaign')].filter(Boolean).join('/');
        } else if (document.referrer) {
            var host = new URL(document.referrer).hostname;
            if (host && host !== location.hostname) found = 'ref:' + host;
        }
        if (found && !sessionStorage.getItem('kbc_source')) sessionStorage.setItem('kbc_source', found);
        var source = sessionStorage.getItem('kbc_source') || 'direct';
        document.querySelectorAll('input[name="source"]').forEach(function (input) { input.value = source; });
    } catch (e) { /* 저장소를 쓸 수 없는 브라우저는 기록하지 않음 */ }

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
