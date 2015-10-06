jQuery(document).ready(function( $ ) {

    $('#filter_wrapper .filter_btn').click(function (e) {
        var q = $(this).parent().data('question');
        var a = $(this).data('answer');
        answerQuestion(q,a);
    });

    /**
     * Front page quiz
     * @param q
     * @param a
     */
    function answerQuestion(q,a) {

        if(q==0 && a=='YES') {
            window.location.href = 'http://gis.pasitesearch.com/default.aspx';
        }

        if(q==0 && a=='NO') {
            $('#filter0').switchClass('on','off');
            $('#filter1').switchClass('off','on');
        }

        if(q==1 && a=='YES') {
            $('#filter1').switchClass('on','off');
            $('#filter2').switchClass('off','on');
        }

        if(q==1 && a=='NO') {
            window.location.href = '/business-assistance/small-business-assistance/';
        }

        if(q==2 && a=='YES') {
            $('#filter1').switchClass('on','off');
            $('#filter2').switchClass('on','off');
            $('#filter3').switchClass('off','on');
        }

        if(q==2 && a=='NO') {
            window.location.href = '/business-assistance/small-business-assistance/';
        }

        if(q==3 && a=='YES') {
            window.location.href = '/governors-action-team-gat/';
        }

        if(q==3 && a=='NO') {
            window.location.href = '/business-assistance/small-business-assistance/';
        }
    }
});