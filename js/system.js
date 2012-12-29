var system = {
    element: function ( id ) {
        var returnVar;
        
        if ( document.getElementById )
            returnVar = document.getElementById(id);
        else if ( document.all )
            returnVar = document.all[id];
        else if ( document.layers )
            returnVar = document.layers[id];
            
        return returnVar;
    },
    newvalue: function ( id, value ) {
        var el = this.element( id );
        if ( el )
            el.value = value;
    },
    newbody: function ( id, body ) {
        var el = this.element( id );
        if ( el )
            el.innerHTML = body;
    },
    disable: function ( id ) {
        var el = this.element( id );
        if ( el )
            el.disabled = true;
    },
    enable: function ( id ) {
        var el = this.element( id );
        if ( el )
            el.disabled = false;
    },
    hide: function ( id ) {
        var el = this.element( id );
        if ( el )
            el.style.display = 'none';
    },
    show: function ( id ) {
        var el = this.element( id );
        if ( el )
            el.style.display = 'block';
    },
    postform: function ( id ) {
        var el = this.element( id );
        if ( el && el.submit )
            el.submit();
    },
    request: function () {
        if ( window.XMLHttpRequest )
            return new XMLHttpRequest();
            
        var req;
        if ( window.ActiveXObject ) 
        {
            try 
            {
                req = new ActiveXObject( "Msxml2.XMLHTTP" );
            } 
            catch ( e )
            {
                try {
                    req = new ActiveXObject( "Microsoft.XMLHTTP" );
                } catch ( e ) {}
            }
        }
        
        return req;
    },
    post: function( url, params, elementid, callback ) {
        this.load( url, params, elementid, callback, "POST" );
    },
    load: function( url, params, elementid, callback, type ) {
        var xmlhttp = this.request();
        var A = this;
        for ( var i in callback )
            A[i] = callback[i];
            
        xmlhttp.onreadystatechange = function() {
            
            A.onLoading != null && A.onLoading()
            
            if ( xmlhttp.readyState == 4 ) {
            
                if (xmlhttp.responseText.search('Error') != -1) {
                
                    A.onError != null && A.onError( xmlhttp, this )
                    
                }
                else if ( xmlhttp.responseText || this.status == 0 || ( this.status >= 200 && this.status < 300 ) ) {
                
                    A.onSuccess != null && A.onSuccess( xmlhttp, this )
                    
                    if ( elementid && system.newbody )
                        system.newbody( elementid, xmlhttp.responseText );
                    
                }
                else {
                
                    A.onFailure != null && A.onFailure( xmlhttp, this )
                    
                    if ( elementid && system.newbody )
                        system.newbody( elementid, this.status );
                    
                }
                
                A.onComplete != null && A.onComplete(xmlhttp, this)
            }
        }
        
        if (type == "POST")
        {
            xmlhttp.open( "POST", url, true );
            xmlhttp.setRequestHeader( "Content-type", "application/x-www-form-urlencoded" );
            xmlhttp.setRequestHeader( "Content-length", params.length );
            xmlhttp.setRequestHeader( "Connection", "close" );
            xmlhttp.send( params );
        }
        else
        {
            xmlhttp.open( "GET", url + params, true );
            xmlhttp.send( null );
        }
    },
};