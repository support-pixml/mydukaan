import { Avatar, Button, Container, CssBaseline, makeStyles, Snackbar, Typography } from '@material-ui/core';
import React, { useRef, useState } from 'react';
import Input from "../../components/UI/Input";
import LockOutlinedIcon from '@material-ui/icons/LockOutlined';
import { Redirect } from 'react-router-dom';
import { signin } from '../../actions/auth';
import {useDispatch, useSelector} from 'react-redux';
import { ValidatorForm } from 'react-material-ui-form-validator';
import { Alert } from '@material-ui/lab';
import { errorConstants } from '../../actions/constants';

const state = {
    phone: '',
    password: ''
};

const useStyles = makeStyles((theme) => ({
    paper: {
        marginTop: theme.spacing(8),
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
    },
    avatar: {
        margin: theme.spacing(1),
        backgroundColor: theme.palette.secondary.main,
    },
    form: {
        width: '100%', // Fix IE 11 issue.
        marginTop: theme.spacing(1),
    },
    submit: {
        margin: theme.spacing(3, 0, 2),
    },
}));

const Signin = () => {

    const [formData, setFormData] = useState(state);
    const classes = useStyles();
    const [errorOpen, setErrorOpen] = useState(true);
    const form = useRef(null);

    const dispatch = useDispatch();

    const auth = useSelector(state => state.auth.authData);
    const error = useSelector(state => state.error.error);

    const userLogin = (e) => {
        e.preventDefault();
        dispatch(signin(formData));
        setErrorOpen(true);
    }

    const errorCloseHandler = () => {
        setErrorOpen(false);
        dispatch({
            type: errorConstants.ERROR_CLEAR
        });
    }
    
    const handleChange = (e) => {
        setFormData({...formData, [e.target.name]: e.target.value});
    }



    if(auth?.user?.name != null)
    {
        return <Redirect to={`/`} />
    }
    
    return (
        <Container component="main" maxWidth="xs">
            <CssBaseline />
            <div className={classes.paper}>
                <Avatar className={classes.avatar}>
                    <LockOutlinedIcon />
                </Avatar>
                <Typography component="h1" variant="h5">
                    Sign in
                </Typography>
                <ValidatorForm className={classes.form} ref={form} noValidate onSubmit={userLogin}>
                    <Input
                        variant="outlined"
                        margin="normal"
                        required
                        value={formData.phone}
                        fullWidth
                        id="phone"
                        label="Phone Number"
                        name="phone"
                        autoFocus
                        handleChange={handleChange}
                        validators={['required']}
                        errorMessages={['this field is required']}
                    />                    
                    <Input
                        variant="outlined"
                        margin="normal"
                        required
                        value={formData.password}
                        fullWidth
                        name="password"
                        label="Password"
                        type="password"
                        id="password"
                        handleChange={handleChange}
                        validators={['required']}
                        errorMessages={['this field is required']}
                    />
                    <Button
                        type="submit"
                        fullWidth
                        variant="contained"
                        color="primary"
                        className={classes.submit}
                    >
                        Sign In
                    </Button>
                </ValidatorForm>
                {error && 
                <Snackbar open={errorOpen} autoHideDuration={3000} onClose={errorCloseHandler}>
                    <Alert severity="error">{error}</Alert>
                </Snackbar>}  
            </div>
        </Container>
    )
}

export default Signin;