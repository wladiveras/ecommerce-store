<script src="{{ mix('/js/manifest.js')}}"></script>
<script src="{{ mix('/js/vendor.js')}}"></script>
<script src="{{ mix('/js/app.js') }}"></script>
<script src="{{ asset('/js/intro.js') }}"></script>
<script src="{{ asset('/js/slick.min.js') }}"></script>
<script src="{{ asset('/js/jquery-searchFilter.js') }}"></script>
<script>

    window.addEventListener("load", function() {
	// store tabs variable
	var myTabs = document.querySelectorAll("ul.nav-tabs > li");
  function myTabClicks(tabClickEvent) {
		for (var i = 0; i < myTabs.length; i++) {
			myTabs[i].classList.remove("active");
		}
		var clickedTab = tabClickEvent.currentTarget;
		clickedTab.classList.add("active");
		tabClickEvent.preventDefault();
		var myContentPanes = document.querySelectorAll(".tab-pane");
		for (i = 0; i < myContentPanes.length; i++) {
			myContentPanes[i].classList.remove("active");
		}
		var anchorReference = tabClickEvent.target;
		var activePaneId = anchorReference.getAttribute("href");
		var activePane = document.querySelector(activePaneId);
		activePane.classList.add("active");
	}
	for (i = 0; i < myTabs.length; i++) {
		myTabs[i].addEventListener("click", myTabClicks)
	}
});
    $(document).ready(function(){

        $("#testejadlog").click(function () {
            alert("vai");
        });

        $("#testejadlog2").click(function () {
            console.log("vai");
            $.ajax({
            headers: { "Content-Type":"application/json", "Authorization":"Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOjcwNjU3LCJkdCI6IjIwMjEwNzA4In0.LwHGzKEaStve2QlPKinoO95c9ZdgyiFcQtICXaIErZo" },
            type: 'POST',
            crossDomain: true,
            dataType: 'jsonp',
            url: "http://www.jadlog.com.br/embarcador/api/pickup/pudos/22290040"
            }).then(function(data) {
                console.log(data.pudoEnderecoList.bairro)
                //$('.greeting-id').append(data.id);
                //$('.greeting-content').append(data.content);
            });
        });

        let carouselStep = 0;
        //let countBlocks = $(".carousel-row").length;

        //$('#prevpage2').hide();        
        $(this).find("div[id^='prevpage']").hide();

        //console.log("total de blocos" + countBlocks);
        //console.log("Step Inicial" + carouselStep);
        $(document).find("div[id^='nextpage']").click(function () {
            //$(".container.carousel").removeClass("carousel-active");
            //$(this).parent().parent().addClass("carousel-active");
            //$(".carousel"+num).attr("teste", ++);
            var num = this.id.split('-')[1];
            let thisStep = $(".carousel" + num).attr("step");
            
            ++thisStep
    
            $(".carousel" + num).attr("step",thisStep);

            
            //console.log("split" + num);

            //let thisstep = $(".carousel-active").attr("teste");
            //console.log(thisstep);

            let countBlocks = $(".carousel" + num + " .carousel-row").length;

            //++window['carouselStep'+num]
            //let thisCarouselStep = carouselStep + num;
            //++(carouselStep + num);
            //console.log(carouselStep1)

            if (thisStep == 0) {
                $(".carousel" + num).find("div[id^='prevpage']").hide();          
            } else {
                $(".carousel" + num).find("div[id^='prevpage']").show();          
            }
            if (thisStep == (countBlocks - 1)) {
                $('#nextpage').hide();          
            } else {
                $('#nextpage').show();
            }
            
            //var rowWidth = $(".carousel-active #carousel-container").width();
            var rowWidth = $(".carousel" + num + " #carousel-container").width();
            if (thisStep == 1) {
                var rowWidth = -Math.abs(rowWidth);             
            } else {
                var rowWidth = -Math.abs(rowWidth*thisStep);
            }         

            //console.log("Step no click next" + carouselStep);
            //console.log(rowWidth);
            $(".carousel" + num + " .carousel-row").first().animate({
                marginLeft: rowWidth
            }, 500, function() {
                // Animation complete.
            });
        });

        $(document).find("div[id^='prevpage']").click(function () {

            var num = this.id.split('-')[1];
            let thisStep = $(".carousel" + num).attr("step");
            
            --thisStep
    
            $(".carousel" + num).attr("step",thisStep);

            let countBlocks = $(".carousel" + num + " .carousel-row").length;
            
            if (thisStep == 0) {
                $(".carousel" + num).find("div[id^='prevpage']").hide();
            } else {
                $(".carousel" + num).find("div[id^='prevpage']").show();
            }
            if (thisStep == countBlocks) {
                $(".carousel" + num).find("div[id^='nextpage']").hide();
            } else {
                $(".carousel" + num).find("div[id^='nextpage']").show();
            }

            var rowWidth = $(".carousel" + num + " #carousel-container").width();
            if (thisStep == 1) {
                var rowWidth = -Math.abs(rowWidth);             
            } else {
                var rowWidth = -Math.abs(rowWidth*thisStep);
            }                               

            //console.log("Step no click prev" + carouselStep);
            //console.log(rowWidth);
            $(".carousel" + num + " .carousel-row").first().animate({
                marginLeft: rowWidth
            }, 500, function() {
                // Animation complete.
            });
        });
    });
    $("#txtSearchPage").keyup(function() {
        var search = $(this).val();
    $(".time-entry").show();
    if (search)
        $(".time-entry").not(":containsNoCase(" + search + ")").hide();
    });

    $("#txtSearchPageTutorial").keyup(function() {
        var search = $(this).val();
        $(".tutorial-filter").show();
        if (search)
        $(".tutorial-filter").not(":containsNoCase(" + search + ")").hide();
    });

    $.expr[":"].containsNoCase = function (el, i, m) {
    var search = m[3];
    if (!search) return false;
    return new RegExp(search,"i").test($(el).text());
    };


    // jQuery Plug-in example
    // $("#txtSearchPagePlugin")
    // .searchFilter({targetSelector: ".time-entry"})

    // $(window).scroll(function() {
    //     if ($(this).scrollTop() > 100) {
    //         $('.header').addClass("fixed");
    //         $('.header-fixed-menu').show();
    //         $('.logo-container').removeClass("col-sm-2");
    //         $('.logo-container').addClass("col-sm-1");
    //         $('.search-container').removeClass("col-sm-7");
    //         $('.search-container').addClass("col-sm-6");            
    //         $('.logo-header').attr("src","{{asset('assets/images/logo-header-fixed.png')}}");
    //     } else {
    //         $('.header').removeClass("fixed");
    //         $('.header-fixed-menu').hide();
    //         $('.logo-header').attr("src","{{asset('assets/images/logo-criafacil.jpg')}}");
    //         $('.logo-container').removeClass("col-sm-1");
    //         $('.logo-container').addClass("col-sm-2");
    //         $('.search-container').removeClass("col-sm-6");
    //         $('.search-container').addClass("col-sm-7");            
    //     }
    // });
</script>
<script type="text/javascript">
    if (window.location.hash === "#_=_"){
    if (history.replaceState) {
        var cleanHref = window.location.href.split("#")[0];
        history.replaceState(null, null, cleanHref);
    } else {
        window.location.hash = "";
    }
}
history.replaceState(null, null, location.href.replace("/public",""))
</script>
@yield('script-bottom')
