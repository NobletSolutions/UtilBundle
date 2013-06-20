var jda = window.jda;

jda.Point = function(x, y)
{
    this.x = x;
    this.y = y;

    this.subtract = function (newPoint) {
        return new jda.Point(this.x - newPoint.x, this.y - newPoint.y);
    };

    this.toString = function () {
        return "x: " + this.x + ", y: " + this.y;
    };
};

/**
 * @class Handles all interactions related to an analog clock component
 * Lets you configure the clock handles (hours and minutes)
 */
jda.AnalogClock = function(clock_id, hour_steps, minute_steps)
{
//  Constants
    this.clock_id = clock_id;
    this.HOUR_STEPS = typeof hour_steps   !== 'undefined' ? hour_steps : 12;
    this.MIN_STEPS  = typeof minute_steps !== 'undefined' ? minute_steps : 60;
    this.HANDLES    = {
        hours: ".handle_hours",
        minutes: ".handle_minutes",
        seconds: ".handle_seconds"
    };
    //  variables
    this.handleHours          = document.querySelector('#' + this.clock_id + ' ' + this.HANDLES.hours);
    this.handleMinutes        = document.querySelector('#' + this.clock_id + ' ' + this.HANDLES.minutes);
    this.handleSeconds        = document.querySelector('#' + this.clock_id + ' ' + this.HANDLES.seconds);
    this.angle                = 0;
    this.center               = new jda.Point(0, 0);
    this.clockChangeEventName = clock_id + 'Change'
    this.clockChangeEvent     = new CustomEvent(this.clockChangeEventName);
    this.clockElement         = document.getElementById(this.clock_id);
    this.currentHandle        = this.handleHours;
    this.hoursFace            = 'img/face_hours.png';
    this.minutesFace          = 'img/face_minutes.png';
    this.isFirstRun           = true;
    this.minStepsSize;
    this.hourStepsSize;
    this.hoursPoint;
    this.minutesPoint;
    
    var aclock = this;

    /**
     * Simulate clock tick
     */
    this.rotateHandle = function()
    {
        if(aclock.currentHandle === aclock.handleHours)
            var stepsSize = aclock.hourStepsSize;
        else
            var stepsSize = aclock.minStepsSize;
        
        aclock.handleSeconds.style.WebkitTransform = 'rotate(' + aclock.angle + 'deg)';
        aclock.angle += stepsSize;
        setTimeout(rotateHandle, 1000);
    };
    00
    12

    /**
     * Gets the current angle given by the mouse coordinates
     * @param {Number} newX The X position relative to the document
     * @param {Number} newY The Y position relative to the document
     */
    this.getAngle = function(newX, newY)
    {
        var newPoint = new jda.Point(newX, newY),
            u = (aclock.currentHandle === aclock.handleHours) ? aclock.hoursPoint.subtract(aclock.center) : aclock.minutesPoint.subtract(aclock.center),
            v = newPoint.subtract(aclock.center),
            u2 = Math.pow(u.x, 2) + Math.pow(u.y, 2),
            v2 = Math.pow(v.x, 2) + Math.pow(v.y, 2),
            value = (u.x * v.x + u.y * v.y) / (Math.sqrt(u2) * Math.sqrt(v2)),
            newAngle = Math.acos(value) * 180 / Math.PI;

        //  left sidedown
        if (newPoint.x < aclock.center.x) {
            newAngle = 360 - newAngle;
        }

        if(aclock.currentHandle === aclock.handleHours)
            var stepsSize = aclock.hourStepsSize;
        else
            var stepsSize = aclock.minStepsSize;
        
        newAngle = Math.round(newAngle / stepsSize) * stepsSize;

        return newAngle;
    };

    /**
     * Sets a new angle for the selected handle
     * @param {Number} value The new angle
     */
    this.setAngle = function(value)
    {
        //  apply rotation in IE9
        if (jda.Utils.isIE()) {
            aclock.currentHandle.style.cssText = '-ms-transform: rotate(' + value + 'deg)';
        } else {
            //  chrome, safari, firefox
            aclock.currentHandle.style[jda.CSSPrefixer.transform] = 'rotate(' + value + 'deg)';
        }

        aclock.angle = value;
    };

    /**
     * Updates the selected handle
     * @param {String} value The handle selector
     */
    this.setCurrentHandle = function(value)
    {
        //  set current handle
        if (value === aclock.HANDLES.hours)
            aclock.currentHandle = aclock.handleHours;
        else
            aclock.currentHandle = aclock.handleMinutes;
    };

    /**
     * Updates the selected handle with a new time
     * @param {Number} value The new time value
     */
    this.updateTime = function(value)
    {
        var newAngle;
        //  set hours
        if (aclock.currentHandle === aclock.handleHours) {
            newAngle = value * aclock.hourStepsSize;
        } 
        //  set minutes
        else {
            newAngle = (value / 60) * aclock.MIN_STEPS * aclock.minStepsSize;
        }
        aclock.setAngle(newAngle);
    };

    /**
     * Dispatch clockChange event when the clock UI has changed
     */
    this.triggerChange = function()
    {
        //  create event
        var time;

        //  hours updated
        if (aclock.currentHandle === aclock.handleHours) {
            aclock.clockChangeEvent.handle = aclock.HANDLES.hours;
            time = aclock.angle / aclock.hourStepsSize;
            if (time == 0) {
                time = 12;
            }
        }
        //  minutes updated
        else
        {
            aclock.clockChangeEvent.handle = aclock.HANDLES.minutes;
            time = aclock.angle * 60 / (aclock.MIN_STEPS * aclock.minStepsSize);
            if (time === 60) {
                time = 0;
            }
        }
        
        aclock.clockChangeEvent.value = jda.Utils.formatDigit(time);
        
        //  trigger event
        document.dispatchEvent(aclock.clockChangeEvent);
    };

    /**
     * Drag handle
     */
    this.document_mousemoveHandler = function(e) {
        var elPos    = jda.elementMouse(e, aclock.clockElement);
        var newAngle = aclock.getAngle(elPos[0], elPos[1]);
        aclock.setAngle(newAngle);
        aclock.triggerChange();
    };

    /**
     * Drop handle
     */
    this.document_mouseupHandler = function(e)
    {
        if(document.removeEventListener)
            document.removeEventListener('mousemove', aclock.document_mousemoveHandler, false);  
        else if(document.detachEvent)
            document.detachEvent('onmousemove', aclock.document_mousemoveHandler);
        
//        if (ev.stopPropagation)
//            ev.stopPropagation();        
//        else if (ev.cancelBubble != null)
//            ev.cancelBubble = true;
    };

    /**
     * Hours handle selected
     */
    this.handleHours_mousedownHandler = function(e)
    {
        e.preventDefault();
        aclock.currentHandle = aclock.handleHours;
        if(document.addEventListener)
        {
            document.addEventListener('mousemove', aclock.document_mousemoveHandler, false);
            document.addEventListener('mouseup', aclock.document_mouseupHandler, false);               
        }
        else if(document.attachEvent)
        {
            document.attachEvent('onmousemove', aclock.document_mousemoveHandler);
            document.attachEvent('onmouseup', aclock.document_mouseupHandler);
        }
    };

    /**
     * Minutes handle selected
     */
    this.handleMinutes_mousedownHandler = function(e)
    {
        e.preventDefault();
        aclock.currentHandle = aclock.handleMinutes;
        if(document.addEventListener)
        {
            document.addEventListener('mousemove', aclock.document_mousemoveHandler, false);
            document.addEventListener('mouseup', aclock.document_mouseupHandler, false);            
        }
        else if(document.attachEvent)
        {
            document.attachEvent('onmousemove', aclock.document_mousemoveHandler);
            document.attachEvent('onmouseup', aclock.document_mouseupHandler);
        }
    }
    
    /**
     * @constructor
     * Initialize component
     */
    //  Get position for the current handles
    this.hoursPoint = new jda.Point(this.handleHours.offsetLeft, this.handleHours.offsetTop);
    this.minutesPoint = new jda.Point(this.handleMinutes.offsetLeft, this.handleMinutes.offsetTop);
    //  set delta
    this.hourStepsSize = 360 / this.HOUR_STEPS;
    this.minStepsSize  = 360 / this.MIN_STEPS;
    //  get clock's center point
    this.center.x = this.handleHours.offsetLeft;
    this.center.y = this.handleHours.offsetTop + this.handleHours.offsetHeight * 0.5;
    //  add event listeners
    if(document.addEventListener)
    {
        this.handleHours.addEventListener('mousedown', this.handleHours_mousedownHandler, false);
        this.handleHours.addEventListener('mouseup', this.handleHours_mouseupHandler, false);
        this.handleMinutes.addEventListener('mousedown', this.handleMinutes_mousedownHandler, false);
        this.handleMinutes.addEventListener('mouseup', this.handleMinutes_mouseupHandler, false);
    }    
    else if(document.attachEvent)
    {
        this.handleHours.attachEvent('onmousedown', this.handleHours_mousedownHandler);
        this.handleHours.attachEvent('onmouseup', this.handleHours_mouseupHandler);
        this.handleMinutes.attachEvent('onmousedown', this.handleMinutes_mousedownHandler);
        this.handleMinutes.attachEvent('onmouseup', this.handleMinutes_mouseupHandler);
    }
};

/**
 * @class Handles all interactions related to an analog clock component
 * Lets you configure the clock handles (hours and minutes)
 */
jda.TimeInput = function(selector, options)
{
    //  Constants
    this.HANDLES = {
            hours: "hours",
            minutes: "minutes",
            meridian: "meridian"
        };
    //  variables
    this.clock = document.getElementById(selector);
    this.form;
    this.handleHours;
    this.handleMinutes;
    this.meridianButton;
    
    this.AnalogClock = new jda.AnalogClock(selector);
    var tinput       = this; //lets just avoid mucking around with scope

    /**
     * @event
     * Hours input focused
     */
    this.handleHours_focusHandler = function(e)
    {
        e.currentTarget.focus();
        e.currentTarget.select();
        tinput.AnalogClock.setCurrentHandle(tinput.AnalogClock.HANDLES.hours);
    };

    /**
     * @event
     * Minutes input focused
     */
    this.handleMinutes_focusHandler = function(e)
    {
        e.currentTarget.focus();
        e.currentTarget.select();
        tinput.AnalogClock.setCurrentHandle(tinput.AnalogClock.HANDLES.minutes);
    };

    this.updateTime = function(handle, value)
    {
        handle.value = value;
        //handle.value =  value;
        //  send data to AnalogClock
        tinput.AnalogClock.updateTime(value);
    };

    /**
     * @event
     * Input text changed
     */
    this.handle_changeHandler = function(e)
    {
        var scope = e.currentTarget,
            time = scope.value.replace(/[^0-9]+$/g, "");

        if (scope.name === "hours") {
            if (time == 0) {
                time = 12;
            }
        } else {
            if (time > 59) {
                time = jda.Utils.formatDigit(0);
            }
        }
        tinput.updateTime(scope, time);
    };

    /**
     * @event
     * Hours/minutes changed from AnalogClock
     */
    this.clockChangeHandler = function(e)
    {
        if (e.handle === tinput.AnalogClock.HANDLES.hours) {
            tinput.handleHours.value = e.value;
        } else {
            tinput.handleMinutes.value = e.value;
        }
    };
    
    this.clockPressHandler = function(e)
    {
        if (e.button !== 0)
            return false;
        
        var ev = e ? e:window.event;
        
        if(tinput.AnalogClock.isFirstRun || tinput.AnalogClock.currentHandle == tinput.AnalogClock.handleMinutes)
        {
            tinput.AnalogClock.setCurrentHandle(tinput.AnalogClock.HANDLES.hours);
            tinput.AnalogClock.isFirstRun = false;
        }
        else
        {
            tinput.AnalogClock.setCurrentHandle(tinput.AnalogClock.HANDLES.minutes);
        }
        
        tinput.AnalogClock.currentHandle.style.visibility = 'visible';
        
        var elPos = jda.clientMouse(e);
        var evt = document.createEvent('MouseEvents');
        evt.initMouseEvent('mousedown', false, true, window, 0, 0, 0, elPos[0], elPos[1], false, false, false, false, 0, null);
        tinput.AnalogClock.currentHandle.dispatchEvent(evt);
        evt.initMouseEvent('mousemove', true, true, window, 0, 0, 0, elPos[0], elPos[1], false, false, false, false, 0, null);
        tinput.AnalogClock.currentHandle.dispatchEvent(evt);
        
        if (ev.stopPropagation)
            ev.stopPropagation();        
        else if (ev.cancelBubble != null)
            ev.cancelBubble = true;
    }
    
    this.clockUpHandler = function(e)
    {
        if(tinput.AnalogClock.isFirstRun || tinput.AnalogClock.currentHandle == tinput.AnalogClock.handleMinutes)
            tinput.AnalogClock.clockElement.style.backgroundImage = "url("+tinput.AnalogClock.hoursFace+")";
        else
            tinput.AnalogClock.clockElement.style.backgroundImage = "url("+tinput.AnalogClock.minutesFace+")";
        
        tinput.AnalogClock.currentHandle.style.visibility = 'hidden';
//        var evt = document.createEvent('MouseEvents');
//        evt.initMouseEvent('mouseup', true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
//        document.dispatchEvent(evt);
    }

    /**
     * Initialize component
     */
    this.initialize = function()
    {
        var currentTime = new Date(),
            currentHours,
            currentMinutes;

        tinput.handleHours = document.querySelector('input.jdaClockHours[data-jdaTrigger='+tinput.clock.id+']');
        tinput.handleMinutes = document.querySelector('input.jdaClockMinutes[data-jdaTrigger='+tinput.clock.id+']');
        tinput.meridianButton = document.querySelector('input.jdaClockMeridian[data-jdaTrigger='+tinput.clock.id+']');

        //  add event handlers
        tinput.handleHours.onfocus = tinput.handleHours_focusHandler;
        tinput.handleHours.onkeyup = tinput.handle_changeHandler;
        tinput.handleMinutes.onfocus = tinput.handleMinutes_focusHandler;
        tinput.handleMinutes.onkeyup = tinput.handle_changeHandler;

        //  set current hour
        if (currentTime.getHours() < 12) {
            currentHours = currentTime.getHours();
            tinput.meridianButton.checked = false;
        }
        else
        {
            currentHours = currentTime.getHours() - 12;
            
            if(!currentHours && tinput.AnalogClock.HOUR_STEPS != 24)
                currentHours = 12;
            
            tinput.meridianButton.checked = true;
        }
        currentHours = jda.Utils.formatDigit(currentHours);
        tinput.AnalogClock.setCurrentHandle(tinput.AnalogClock.HANDLES.hours);
        tinput.updateTime(tinput.handleHours, currentHours);

        //  set current minutes
        currentMinutes = currentTime.getMinutes();
        tinput.AnalogClock.setCurrentHandle(this.AnalogClock.HANDLES.minute);
        currentMinutes = jda.Utils.formatDigit(currentMinutes);
        tinput.updateTime(tinput.handleMinutes, currentMinutes);

        //  bind for AnalogClock.change event
        if(document.addEventListener)
        {
            document.addEventListener(tinput.AnalogClock.clockChangeEventName, tinput.clockChangeHandler, false);
            tinput.AnalogClock.clockElement.addEventListener('mousedown', tinput.clockPressHandler, false);
            document.addEventListener('mouseup', tinput.clockUpHandler, false);
        }
        else if(document.attachEvent)
        {
            document.attachEvent('on'+tinput.AnalogClock.clockChangeEventName, tinput.clockChangeHandler);
            tinput.AnalogClock.clockElement.attachEvent('onMousedown', tinput.clockPressHandler);
            document.attachEvent('onMouseup', tinput.clockUpHandler);
        }
    };
    
//    if (this.clock)
//        alert('initing');
        
    this.initialize();
};

jda.ClockPicker = function(selector, options)
{
    /**
     * Clock picker DOM container
     * @type {HTMLElement}
     * @private
     */
    this._element;

    /**
     * Time input fields
     * @type {TimeInput}
     */
    this._timeInput;

    /**
     * Analog clock component
     * @type {AnalogClock}
     */
    this._analogClock;

    /**
     * Module default Settings
     * @type {Object}
     */
    this.SETTINGS     = {};
    this._timeInput   = new parent.TimeInput();
    this._analogClock = new parent.AnalogClock();
};

jda.clientMouse = function(e)
{
    var posx = 0;
    var posy = 0;
    if (!e) var e = window.event;
    if (e.pageX || e.pageY)     {
        posx = e.pageX;
        posy = e.pageY;
    }
    else if (e.clientX || e.clientY)    {
        posx = e.clientX + document.body.scrollLeft
            + document.documentElement.scrollLeft;
        posy = e.clientY + document.body.scrollTop
            + document.documentElement.scrollTop;
    }
    
    return [posx, posy];
};

jda.cumulativeOffset = function(element)
{
    var top  = 0;
    var left = 0;
    
    do
    {
        top     += element.offsetTop  || 0;
        left    += element.offsetLeft || 0;
        element  = element.offsetParent;
    }
    while(element);

    return [left, top];
};

jda.elementMouse = function(event, element)
{
    elPos  = jda.cumulativeOffset(element);
    curPos = jda.clientMouse(event);    
    
    return [curPos[0]-elPos[0], curPos[1]-elPos[1]];
};