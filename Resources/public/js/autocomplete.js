Event.observe(window, 'load', function(event)
{
    document.body.insert({bottom:'<div class="autocompleteResult" id="autocomplete_result"></div>'});
    activateAutocompleters();
});
 
activateAutocompleters = function()
{
    $$('input[data-autocomplete=true]').each(function(input)
    {
        if(!input.autocompleterBuilt)
        {
            var options = {};
            options['listTitle'] = input.getAttribute('data-autocomplete-listtitle') ? input.getAttribute('data-autocomplete-tokenize') : 'Selected Options:';
           
            if(input.getAttribute('data-autocomplete-tokenize') === 'true')
            {
                options['paramName']          = 'value';
                options['afterUpdateElement'] = tokenizeResults;
               
                var id         = input.id;
                var name       = input.name;
                var tokendata  = input.getValue();
               
                input.removeAttribute('name');
               
                var tokenholder = new Element('div',
                {
                    'class': 'autocomplete-token',
                    'id':    id + '_token'
                });
                var tokenvalue = new Element('input',
                {
                    'type': 'hidden',
                    'id':    id + '_token_value',
                    'name':  name,
                    'value': tokendata,
                    'data-autocomplete-multiple': input.getAttribute('data-autocomplete-multiple')
                });
               
                var wrap = new Element('div', {'class':'autocompleter'});
               
                input.wrap(wrap);            
                input.insert({after:tokenholder});
                input.insert({after:tokenvalue});
               
                if(input.getAttribute('data-autocomplete-secondary-field'))
                {
                    var sec = JSON.parse(input.getAttribute('data-autocomplete-secondary-field'));
                    var tgt = input.id.replace(sec.s, sec.r) + '_token_value';
                    
                    var s   = $(tgt);
                    if(!s)
                    {
                        //TODO This should probably be removed.. in what case would this work??
                        tgt = input.id.replace(sec.s, sec.r);
                        s   = $(tgt);
                    }
   
                    if(s)
                    {
                        options['parameters'] = 'secondary-field=' + s.getValue();
                        s.observe('token:success', function(changeevent)
                        {
                            input.autocompleter.options.defaultParams = 'secondary-field='+s.getValue();
                        });
                        s.observe('change', function(changeevent)
                        {
                            input.autocompleter.options.defaultParams = 'secondary-field='+s.getValue();
                        });
                    }
                }
               
                if(tokendata)
                {
                    tokendata = JSON.parse(tokendata);                
                    tokenholder.insert(options['listTitle']);
                }
                else
                    tokendata = {};
               
                for(var key in tokendata)
                {
                    if(tokendata.hasOwnProperty(key))
                    {
                        var token = new Element('a',
                        {
                            'class':      'autocompleteDelete',
                            'data-id':    key,
                            'data-field': tokenvalue.id
                        }).update(tokendata[key]);
       
                        token.observe('click', function(event)
                        {
                            observeToken(event);
                        });
                       
                        tokenholder.insert(token);
                    }
                }
               
                input.setValue('');
            }
           
            input.autocompleter = new Ajax.Autocompleter(input, 'autocomplete_result', input.getAttribute('data-autocomplete-href'), options);
            input.autocompleterBuilt = true;
        }
    });  
};
 
tokenizeResults = function(field, li)
{
    var id          = li.getAttribute('data-id');
    var value       = li.getAttribute('data-string');
    var tokenfield  = $(field.id+'_token_value');
    var tokenholder = $(field.id+'_token');
    var tokenvals   = tokenfield.getValue();    
    var listTitle   = field.getAttribute('data-autocomplete-listtitle') ? field.getAttribute('data-autocomplete-tokenize') : 'Selected Options:';
   
    if(tokenvals && field.getAttribute('data-autocomplete-multiple') === 'true')
    {
        tokenvals = JSON.parse(tokenvals);
        if(!Object.values(tokenvals).length)
            tokenholder.insert(listTitle);
    }
    else
    {
        tokenholder.insert(listTitle);
        tokenvals = {};
    }
   
    if(!tokenvals.hasOwnProperty(id)) //prevent duplicates
    {
        tokenvals[id] = value;
 
        tokenfield.setValue(JSON.stringify(tokenvals));
 
        field.setValue('');
 
        var token = new Element('a',
        {
            'class':      'autocompleteDelete',
            'data-id': id,
            'data-field': tokenfield.id
        }).update(value);
 
        token.observe('click', function(event)
        {
            observeToken(event);
        });
 
        if(field.getAttribute('data-autocomplete-multiple') !== 'true')
        {
            tokenholder.update('');
            tokenholder.insert(listTitle);
        }
       
        tokenholder.insert(token);
        tokenfield.fire('token:success');
    }
};
 
observeToken = function(event)
{    
    event.stop();
 
    var t = event.findElement('a');
    var f = $(t.getAttribute('data-field'));
    var v = t.getAttribute('data-id');
 
    var val = JSON.parse(f.getValue());
    delete val[v];
    f.setValue(JSON.stringify(val));
   
    if(f.getAttribute('data-autocomplete-multiple') !== 'true' || !Object.values(val).length)
        t.up().update('');
    else
        t.remove();
};
