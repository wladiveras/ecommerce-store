$.expr[":"].containsNoCase = function (el, i, m) {
    var search = m[3];
    if (!search) return false;
      return new RegExp(search,"i").test($(el).text());
};

$.fn.searchFilter = function (options) {
    var opt = $.extend({
        // target selector
        targetSelector: "",
        // number of characters before search is applied
        charCount: 1
    }, options);

    return this.each(function () {
        var $el = $(this);
        $el.on("keyup",function () {
            var search = $(this).val();

            var $target = $(opt.targetSelector);
            $target.show();

            if (search && search.length >= opt.charCount)
                $target.not(":containsNoCase(" + search + ")").hide();
        });
    });
};