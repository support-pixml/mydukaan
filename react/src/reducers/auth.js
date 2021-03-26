import * as actionType from '../actions/constants';

const authReducer = (state = {authData: null}, action) => {
    switch(action.type) {
        case actionType.SIGN_IN:
            localStorage.setItem('profile', JSON.stringify({...action?.data}));
            return {...state, authData: action?.data}
        case actionType.SIGN_UP:
            return {...state, authData: action?.data}
        case actionType.LOG_OUT:
            localStorage.clear();
            return {...state, authData: null}
        default:
            return state;
    }
}

export default authReducer;