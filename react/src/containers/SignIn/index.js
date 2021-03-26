import { Avatar, Button, Container, CssBaseline, Grid, makeStyles, Typography } from '@material-ui/core';
import React, { useState } from 'react';
import Input from "../../components/UI/Input";
import LockOutlinedIcon from '@material-ui/icons/LockOutlined';
import { Link, Redirect } from 'react-router-dom';
import { signin } from '../../actions/auth';
import {useDispatch} from 'react-redux';

const initialState = {
    email: '',
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

    const [formData, setFormData] = useState(initialState);
    const classes = useStyles();

    // const [error, setError] = useState('');
    const dispatch = useDispatch();

    const user = localStorage.getItem('profile');

    const userLogin = (e) => {
        e.preventDefault();
        dispatch(signin(formData));
    }

    const handleChange = (e) => {
        setFormData({...formData, [e.target.name]: e.target.value});
    }

    if(user)
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
                <form className={classes.form} noValidate onSubmit={userLogin}>
                    <Input
                        variant="outlined"
                        margin="normal"
                        required
                        fullWidth
                        id="email"
                        label="Email Address"
                        name="email"
                        autoFocus
                        type="email"
                        handleChange={handleChange}
                    />                    
                    <Input
                        variant="outlined"
                        margin="normal"
                        required
                        fullWidth
                        name="password"
                        label="Password"
                        type="password"
                        id="password"
                        handleChange={handleChange}
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
                    <Grid container justify="flex-end">
                        <Grid item>
                        <Link to="/signup">
                            {"Don't have an account? Sign Up"}
                        </Link>
                        </Grid>
                    </Grid>
                </form>
            </div>
        </Container>
    )
}

export default Signin;