(function(d) {
    var site = drupalSettings.parsely['parsely-include'].apikey, // replace with the domain of your site (e.g. parsely.com)
        b = d.body,
        e = d.createElement("div");
    e.id = 'parsely-cfg';
    e.src = '//cdn.parsely.com/keys/'+site+'/p.js';
    e.setAttribute('data-parsely-site', site);
    e.setAttribute('data-cfasync', 'false');
    b.appendChild(e);
})(document);
