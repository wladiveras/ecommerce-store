Object.toFormData = (obj, fd, prepend) => {
    let val,key
    fd = fd || new FormData
    
    for (key in obj) {
        val = obj[key]
        key = prepend ? `${prepend}[${key}]` : key
        
        if (typeof val == 'object' && !(val instanceof File))
            Object.toFormData(val, fd, key)
        else
            fd.append(key, val)
    }
    return fd;
}