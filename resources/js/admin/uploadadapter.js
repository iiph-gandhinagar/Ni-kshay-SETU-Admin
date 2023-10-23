    export default class UploadAdapter {
        constructor( loader ) {
            // The fileToUpload loader instance to use during the upload.
            this.loader = loader;
        }
    
        // Starts the upload process.
        upload() {
            console.log("fileToUpload ----->",JSON.stringify(this.loader.file));
            return this.loader.file
                .then( fileToUpload => new Promise( ( resolve, reject ) => {
                    console.log(fileToUpload);
                    console.log(resolve);
                    this._initRequest();
                    this._initListeners( resolve, reject, fileToUpload );
                    this._sendRequest( fileToUpload );
                } ) );
        }
    
        // Aborts the upload process.
        abort() {
            if ( this.xhr ) {
                this.xhr.abort();
            }
        }
    
        // Initializes the XMLHttpRequest object using the URL passed to the constructor.
        _initRequest() {
            const xhr = this.xhr = new XMLHttpRequest();
    
            xhr.open( 'POST','/admin/wysiwyg-media', true );
            xhr.responseType = 'json';
            xhr.uploaded =true;
            xhr.fileToUpload =this.loader.fileToUpload;

            // xhr.setRequestHeader('Content-Type',"multipart/form-data");
            // token = $('meta[name=csrf-token]').attr('content');
            // token = document.querySelector('meta[name="csrf-token"]').content;
            // xhr.setRequestHeader("_token", "csrf_token()");
            xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name=csrf-token]').attr('content'));
        }
    
        // Initializes XMLHttpRequest listeners.
        _initListeners( resolve, reject, fileToUpload ) {
            const xhr = this.xhr;
            const loader = this.loader;
            const genericErrorText = `Couldn't upload fileToUpload: ${ fileToUpload.name }.`;
    
            xhr.addEventListener( 'error', () => reject( genericErrorText ) );
            xhr.addEventListener( 'abort', () => reject() );
            xhr.addEventListener( 'load', () => {
                const response = xhr.response;
    
                if ( !response || response.error ) {
                    return reject( response && response.error ? response.error.message : genericErrorText );
                }
                console.log( "response --->" ,response,response.file );
                resolve( {
                    default: response.file
                } );
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
    
        // Prepares the data and sends the request.
        _sendRequest( fileToUpload ) {
            // Prepare the form data.
            const data = new FormData();
    
            data.append( 'fileToUpload', fileToUpload );
    
            // Send the request.
            this.xhr.send( data );
        }
    }