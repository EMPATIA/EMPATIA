/**   EXTERNAL LIBRARIES   **/

// lodash, bootstrap, jquery, axios
import './libraries';


/**   APP CODE   **/

/*
* Reloads functions on Livewire component updates
* */
window.reloadFunctions = {
    instances: {},
    debug: false,
    allowFunctionOverride: false,

    add: function(name, callback, args){
        if( typeof name != 'string' ){
            this.logDebug('invalid name', name);
            return false;
        }

        if( !this.allowFunctionOverride && this.exists(name) ){
            this.logDebug(`name ${name} already in use`);
            return false;
        }

        if( typeof callback != 'function' ){
            this.logDebug('invalid callback', callback);
            return false;
        }

        if( typeof args != 'undefined' && typeof args != 'object' ){
            this.logDebug('invalid args', args);
            return false;
        }

        this.instances[name] = {
            name,
            callback,
            args
        }

        return true;
    },
    remove: function(name){
        return this.exists(name) && delete this.instances[name];
    },
    exists: function (name){
        if( typeof name != 'string' ){
            this.logDebug('invalid name', name);
            return false;
        }

        return typeof this.instances[name] != 'undefined';
    },

    getArguments: function(name){
        if( ! this.exists(name) ){
            this.logDebug('no instance with the provided name', name);
            return false;
        }

        return this.instances[name].args;
    },
    setArguments: function(name, args){
        if( ! this.exists(name) ){
            this.logDebug('no instance with the provided name', name);
            return false;
        }

        if( typeof args != 'undefined' && typeof args != 'object' ){
            this.logDebug('invalid args', args);
            return false;
        }

        return this.instances[name].args = args;
    },

    call: function(name, ...args){
        if( ! this.exists(name) ){
            this.logDebug('no instance with the provided name', name);
            return false;
        }

        // override args if provided in this call
        if( !args.length && typeof this.instances[name].args != 'undefined' ){
            args = typeof this.instances[name].args == 'object' ? Object.values(this.instances[name].args) : [];
        }

        return this.instances[name].callback(...args);
    },
    do: function (){
        Object.keys(this.instances).map((key, index) => {
            this.call(key);
        });
    },

    logDebug: function(message, ...vars){
        if( !this.debug ) return;
        console.log(`functionsToReload: ${message}`, ...vars);
    }
}
