import {authConstants} from '../actions/constants';

const authReducer = (state = {authData: null, error: null}, action) => {
    switch(action.type) {
        case authConstants.LOGIN_REQUEST:
            localStorage.setItem('access_token', action?.data.access_token);
            localStorage.setItem('user', JSON.stringify({...action?.data.user}));
            return {...state, authData: action?.data}
        case authConstants.LOGIN_FAILURE:
            return {...state, error: action.payload.error}
        case authConstants.LOGOUT_REQUEST:
            return {...state};
        case authConstants.LOGOUT_SUCCESS:
            localStorage.clear();
            return {...state, authData: null}
        case authConstants.LOGOUT_FAILURE:
            localStorage.clear();
            return {...state, authData: action.payload.error};
        default:
            return state;
    }
}

export default authReducer;