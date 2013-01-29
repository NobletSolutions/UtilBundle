Event.observe(window, 'load', function(event)
{
    document.body.insert({bottom:'<div class="autocompleteResult" id="autocomplete_result"></div>'});
    $$('input[data-autocomplete=true]').each(function(input)
    {
        var options = {};
        
        if(input.getAttribute('data-autocomplete-tokenize') == 'true')
        {
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
            
            input.insert({before:tokenholder});
            input.insert({after:tokenvalue});
            
            if(tokendata && input.getAttribute('data-autocomplete-multiple') == 'true')
                tokendata = JSON.parse(tokendata);
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
        
        new Ajax.Autocompleter(input, 'autocomplete_result', input.getAttribute('data-autocomplete-href'), options);
    });
});

tokenizeResults = function(field, li)
{
    var id          = li.getAttribute('data-id');
    var value       = li.getAttribute('data-string');
    var tokenfield  = $(field.id+'_token_value');
    var tokenholder = $(field.id+'_token');
    var tokenvals   = tokenfield.getValue();
    
    if(tokenvals && field.getAttribute('data-autocomplete-multiple') == 'true')
        tokenvals = JSON.parse(tokenvals);
    else
        tokenvals = {};
    
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

        if(field.getAttribute('data-autocomplete-multiple') != 'true')
            tokenholder.update('');
        
        tokenholder.insert(token);
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

    t.remove();
};