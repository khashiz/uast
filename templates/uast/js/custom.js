jQuery(document).ready(function(){
    jQuery('.marquee').marquee({
        //speed in milliseconds of the marquee
        duration: 10000,
        //'left' or 'right'
        direction: 'right',
        //true or false - should the marquee be duplicated to show an effect of continues flow
        duplicated: true,
        pauseOnHover: true
    });
});