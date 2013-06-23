document.observe("dom:loaded", function()
{
    $$('input[data-clockField=clockHours]').each(function(clockhour){
        var clockminute    = false;
        var clockeridian   = false;
        var group          = clockhour.getAttribute('data-clockGroup');
        
        $$('input[data-clockField=clockMinutes][data-clockGroup='+group+']').each(function(el){
            clockminute = el;
        });
        
        $$('input[data-clockField=clockMeridian][data-clockGroup='+group+']').each(function(el){
            clockmeridian = el;
        });
        
        var meridianlabel  = clockmeridian.next('label');
        
        var clock_id = group+'_clock';
        
        clockhour.writeAttribute('data-jdaTrigger', clock_id);
        clockminute.writeAttribute('data-jdaTrigger', clock_id);        
        clockmeridian.writeAttribute('data-jdaTrigger', clock_id);
        clockhour.addClassName('jdaClockHours');
        clockminute.addClassName('jdaClockMinutes');
        clockmeridian.addClassName('jdaClockMeridian');
        
        var clock_elements = ''+
        '<div class="clockWrapper" id="clock_wrapper_'+clock_id+'">'+
        '<h3>Click and Drag to set time</h3>'+
        '<div class="js_setclock" id="'+clock_id+'">'+
            '<div class="handle_minutes"><div class="skin">00</div></div>'+
            '<div class="handle_hours"><div class="skin">12</div></div>'+
            '<div class="handle_seconds"><div class="skin">00</div></div>'+
        '</div></div>';
        
        var clockButton = new Element('button', {class:'showClock', type:'button'});
        clockButton.insert('Show Clock');
        clockButton.observe('click', function(click)
        {
            click.stop();
            $(clock_id).isFirstRun = true;
            $('clock_wrapper_'+clock_id).addClassName('show');
        });
        
        clockhour.up('div.gsClockPickerFields').insert({before:clock_elements, bottom:clockButton});
        
        var wrapper = new Element('div', {'class':'meridian_mask'});
        wrapper.insert({top:clockmeridian, bottom:meridianlabel});
        clockminute.insert({after:wrapper});
        
        $(clock_id).observe('minutesUp', function(minup)
        {
            $('clock_wrapper_'+clock_id).removeClassName('show');
        });
        
        new window.jda.TimeInput(clock_id);
    });
});