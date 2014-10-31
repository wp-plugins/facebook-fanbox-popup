function clearCookie(name, domain, path){
    var domain = domain || document.domain;
    var path = path || "/";
    document.cookie = name + "=; expires=" + +new Date + ";  path=/";
    alert('Cookies deleted!');
    return false;
}