<script>
    class MyUploadAdapter {
        constructor( loader ) {
            this.loader = loader;
            console.log("loader------>",this.loader);
        }
        
        upload() {
            return this.loader.file
                .then( file => new Promise( ( resolve, reject ) => {
                    this._initRequest();
                    this._initListeners( resolve, reject, file );
                    this._sendRequest( file );
                } ) );
        }
     
        abort() {
            if ( this.xhr ) {
                this.xhr.abort();
            }
        }
     
        _initRequest() {
            const xhr = this.xhr = new XMLHttpRequest();
     
            xhr.open( 'POST', "{{route('brackets/media::upload', ['_token' => csrf_token() ])}}", true );
            xhr.responseType = 'json';
        }
     
        _initListeners( resolve, reject, file ) {
            const xhr = this.xhr;
            const loader = this.loader;
            const genericErrorText = `Couldn't upload file: ${ file.name }.`;
     
            xhr.addEventListener( 'error', () => reject( genericErrorText ) );
            xhr.addEventListener( 'abort', () => reject() );
            xhr.addEventListener( 'load', () => {
                const response = xhr.response;
     
                if ( !response || response.error ) {
                    return reject( response && response.error ? response.error.message : genericErrorText );
                }
     
                resolve( response );
            } );
     
            if ( xhr.upload ) {
                xhr.upload.addEventListener( 'progress', evt => {
                    if ( evt.lengthComputable ) {
                        loader.uploadTotal = evt.total;
                        loader.uploaded = evt.loaded;
                    }
                } );
            }
        }
     
        _sendRequest( file ) {
            const data = new FormData();
     
            data.append( 'file', file );
     
            this.xhr.send( data );
        }
    }
     
    function MyCustomUploadAdapterPlugin( editor ) {
        editor.plugins.get( 'FileRepository' ).createUploadAdapter = ( loader ) => {
            return new MyUploadAdapter( loader );
        };
    }
    
    // DecoupledEditor
    // .create( document.querySelector( '#editor' ), {
    //     extraPlugins: [ MyCustomUploadAdapterPlugin ],
    //     cloudServices: {
    //     }
    // } )
    // .then( editor => {
    //     const toolbarContainer = document.querySelector( '#toolbar-container' );

    //     toolbarContainer.appendChild( editor.ui.view.toolbar.element );

    //     window.editor = editor;
    // } )
    // .catch( err => {
    //     console.error( err );
    // } );
    const editor = ClassicEditor
        .create( document.querySelector( '.editor' ), {
            extraPlugins: [ MyCustomUploadAdapterPlugin ],
        } )
        .catch( error => {
            console.error( error );
        } );

        console.log("document.getElementById('.editor').innerHTML",document.getElementById('.editor'));
    </script>