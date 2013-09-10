document.observe("dom:loaded", function()
{
    var clockforms = new Array();
    $$('input[data-clockField=clockHours]').each(function(clockhour){
        var f = clockhour.up('form');
        if(!f.found)
        {
            f.found = true;
            clockforms.push(f);
        }
        var clockminute    = false;
        var clockeridian   = false;
        var group          = clockhour.getAttribute('data-clockGroup');
        var h_increment    = clockhour.getAttribute('data-increment');
        if(!h_increment)
            h_increment = 1;
        
        $$('input[data-clockField=clockMinutes][data-clockGroup='+group+']').each(function(el){
            clockminute = el;
        });
        
        $$('input[data-clockField=clockMeridian][data-clockGroup='+group+']').each(function(el){
            clockmeridian = el;
        });
        
        var m_increment    = clockminute.getAttribute('data-increment');
        if(!m_increment)
            m_increment = 1;
        
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
        '<div id="clock_value_'+clock_id+'" class="clockValue"></div>'+
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
        
        new window.jda.TimeInput(clock_id, {'h_increment':12/h_increment, 'm_increment':60/m_increment});
    });
    
    for(var i=0; i<clockforms.length; i++)
    {
        var f = clockforms[i];
        f.observe('submit', function(ev)
        {
            f.select('input.jdaClockHours').each(function(input)
            {
                var merid = input.next('.meridian_mask').down('input.jdaClockMeridian');
                if(merid.checked)
                    input.setValue(parseInt(input.getValue())+12);
            });
        });
    }
});