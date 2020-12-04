Handlebars.registerHelper('if_eq', function(a, b, opts) {
    if (a == b)
        return opts.fn(this);
    else
        return opts.inverse(this);
});

Handlebars.registerHelper('isInArray', function(list, elem, options) {
    if(list.indexOf(elem) > -1) {
        return options.fn(this);
    }
    return options.inverse(this);
});

Handlebars.registerHelper("math", function(lvalue, operator, rvalue, options) {
    lvalue = parseFloat(lvalue);
    rvalue = parseFloat(rvalue);

    return {
        "+": lvalue + rvalue,
        "-": lvalue - rvalue,
        "*": lvalue * rvalue,
        "/": lvalue / rvalue,
        "%": lvalue % rvalue
    }[operator];
});