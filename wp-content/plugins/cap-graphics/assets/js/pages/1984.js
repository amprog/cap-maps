jQuery(document).ready(function( $ ) {


    //hide all divs initially
    $('#food_type_drop').hide();
    $('#food_manu_drop').hide();
    $('#cert_drop').hide();

    //drop downs
    $(".drop" ).live( "click", function() {
        var $this = $(this);
        var div = $this.find('a').data('drop');
        $this.toggleClass('up');
        $('#'+div).slideToggle();
    });

    var filters = {};
    //input instead of label so it doesn't fire twice onoffswitch
    $('.onoffswitch-checkbox').click(function(e){
        var $this = $(this);


        var checked = $this.is(":checked");
        //console.log($this.parents().data('value'));
        if(checked) {
            $($this.parents().data('value')).fadeIn();
        } else {
            $($this.parents().data('value')).fadeOut();
        }

        //update count
        $('#food_count').html($('.item:not([style*="display: none"])').length);
    });


    if ($(window).width() < 768) {
        $('div .swm_horizontal_menu').addClass('h_responsive');
    }


    //filter by icons only on projects page
    if($('.projects .project_meta').length) {
        $('.project_meta').on('click', 'a', function () {
            var filterValue = $(this).attr('data-filter');
            $('.swm_portfolio_sort').isotope({filter: filterValue});
            return false;
        });
    }

    //enter listener
    $("#fuse_search").bind("keydown", function(e) {
        if (e.keyCode == 13) { $( ".fuse_submit" ).trigger( "click" ); }
    });

    //fuzzy search
    $(".fuse_submit" ).live( "click", function() {
        var val = $('#fuse_search').val();
        if(val=='') {
            $('#fuse_search').addClass('email_error');
            $('#fuse_search').attr('placeholder','Please enter a search term');
            return false;
        }
        $('#fuse_search').removeClass('email_error');
        var items = [];
        $('.item_container article h4').each( function( i, e ) {
            items.push($(e).text());
        });

        var options = {
            caseSensitive: false,
            shouldSort: true,
            threshold: 0.3,
            location: 0,
            maxPatternLength: 32
        };

        var f      = new Fuse(items,options);
        var result = f.search(val);
        $('.item_container article').addClass('h');
        $.each( result, function( index, value ){
            $('#item'+value).removeClass("h");
        });
    });

    //show all
    $(".fuse_all" ).live( "click", function() {
        $('.item_container article').removeClass('h');
    });



    //check all not working as expected
    /*
    $('.check_all').toggle(function(){
        var $this = $(this);

        $buttonGroup = $(this).parents().parents().parents().attr('id');
        $drop = '#'+$buttonGroup+' input:checkbox';

        //get from data-section
        console.log('1 button group: '+$buttonGroup);
        console.log('1 drop: '+$drop);

        //$($drop).removeAttr('checked');
        //$('#cold-storage-switch0').removeAttr('checked');
        //

        $($drop).css('marginLeft', '0!important;');

        $this.parent().toggleClass('m0');

        $( ".onoffswitch-checkbox" ).trigger( "click" );

    },function(){
        console.log('2 button group: '+$buttonGroup);
        console.log('2 drop: '+$drop);
        $('#cold-storage-switch0').attr('checked','checked');

        //$($drop).attr('checked','checked');
        $(this).parent().toggleClass('m0');
        $( ".onoffswitch-checkbox" ).trigger( "click" );
    })
    */

});