const ErrorMixins = {
    data(){
        return {
            errors:[]
        }
    },
    methods: {
        hasInErrors(error) {
            if (this.errors.length === 0) return false;
            return Object.keys(this.errors).indexOf(error) !== -1;
        },
        getError(error) {
            return this.errors[error][0];
        },
        cleanError(error) {
            delete this.errors[error];
        },
    },
};
export default ErrorMixins;
