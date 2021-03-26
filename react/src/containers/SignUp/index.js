import { Avatar, Button, Container, CssBaseline, Grid, makeStyles, Typography } from '@material-ui/core';
import React, { useState } from 'react';
import { Link, Redirect } from 'react-router-dom';
import LockOutlinedIcon from '@material-ui/icons/LockOutlined';
import Input from '../../components/UI/Input';
import {useDispatch} from 'react-redux';
import { signup } from '../../actions/auth';

const initialState = {
    firstName: '', lastName: '', email: '', password: '', confirmPassword: ''
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
        marginTop: theme.spacing(3),
    },
    submit: {
        margin: theme.spacing(3, 0, 2),
    },
}));

const Signup = () => {
    const classes = useStyles();
    const [formData, setFormData] = useState(initialState);
    const dispatch = useDispatch();
    const user = localStorage.getItem('profile');

    if(user)
    {
        return <Redirect to={`/`} />
    }

    const userRegister = (e) => {
        e.preventDefault();
        dispatch(signup(formData));
    }

    const handleChange = (e) => {
        setFormData({...formData, [e.target.name]: e.target.value});
    }

    return (
        <Container component="main" maxWidth="xs">
            <CssBaseline />
            <div className={classes.paper}>
                <Avatar className={classes.avatar}>
                <LockOutlinedIcon />
                </Avatar>
                <Typography component="h1" variant="h5">
                Sign up
                </Typography>
                <form className={classes.form} noValidate onSubmit={userRegister}>
                <Grid container spacing={2}>
                    <Grid item xs={12}>
                        <Input
                            name="name"
                            variant="outlined"
                            required
                            fullWidth
                            id="fullName"
                            label="Full Name"
                            placeholder="Full Name"
                            autoFocus
                            type="email"
                            handleChange={handleChange}
                        />
                    </Grid>                    
                    <Grid item xs={12}>
                        <Input
                            variant="outlined"
                            required
                            fullWidth
                            id="email"
                            label="Email Address"
                            placeholder="Email Address"
                            name="email"
                            handleChange={handleChange}
                        />
                    </Grid>
                    <Grid item xs={12}>
                        <Input
                            variant="outlined"
                            required
                            fullWidth
                            name="password"
                            label="Password"
                            placeholder="Password"
                            type="password"
                            id="password"
                            handleChange={handleChange}
                        />
                    </Grid>
                    <Grid item xs={12}>
                        <Input
                            variant="outlined"
                            required
                            fullWidth
                            name="confirmPassword"
                            label="Confirm Password"
                            placeholder="Confirm Password"
                            type="password"
                            id="confirmPassword"
                            handleChange={handleChange}
                        />
                    </Grid>
                </Grid>
                <Button
                    type="submit"
                    fullWidth
                    variant="contained"
                    color="primary"
                    className={classes.submit}
                >
                    Sign Up
                </Button>
                <Grid container justify="flex-end">
                    <Grid item>
                        <Link to="/signin">
                            Already have an account? Sign in
                        </Link>
                    </Grid>
                </Grid>
                </form>
            </div>
        </Container>
    )
}

export default Signup;