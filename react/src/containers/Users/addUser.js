import { Backdrop, Button, Fade, FormControl, Grid, InputLabel, makeStyles, Modal, Select, Snackbar, Typography } from '@material-ui/core';
import { Alert } from '@material-ui/lab';
import React, { useEffect, useRef, useState } from 'react';
import { ValidatorForm } from 'react-material-ui-form-validator';
import { useDispatch, useSelector } from 'react-redux';
import { userConstants } from '../../actions/constants';
import { addUser, updateUser } from '../../actions/users';
import Input from '../../components/UI/Input';

const initialState = {
    name: '', phone: '', password: '', role: ''
};

const useStyles = makeStyles((theme) => ({
    modal: {
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
    },
    paper: {
        backgroundColor: theme.palette.background.paper,
        border: '2px solid #000',
        boxShadow: theme.shadows[5],
        padding: theme.spacing(2, 4, 3),
        width: '500px'
    },
    form: {
        width: '100%', // Fix IE 11 issue.
        marginTop: theme.spacing(3),
    },
    submit: {
        margin: theme.spacing(3, 0, 2),
    },    
    formControl: {
        width: '100%', 
    },
}));

const AddUser = ({handleClose, open, user}) => {
    const classes = useStyles();
    const [formData, setFormData] = useState(initialState);
    const [errorOpen, setErrorOpen] = useState(true);    
    const dispatch = useDispatch();
    const form = useRef(null);

    const error = useSelector(state => state.error.error);

    useEffect(() => {
        if(user)
        {
            setFormData({ ...formData, long_id: user.long_id, name: user.name, phone: user.phone, role: user.role, password: ''});
        }
        else
        {
            setFormData(initialState);
        }
    }, [user]);

    const submitUser = async (e) => {
        e.preventDefault();
        if(user)
        {
            dispatch(updateUser(formData));
        }
        else {
            dispatch(addUser(formData));     
        }
        setErrorOpen(true);
        setFormData(initialState);
    }    
    
    const handleChange = (e) => {
        setFormData({...formData, [e.target.name]: e.target.value.trim()});
    }

    return (
        <Modal
            aria-labelledby="transition-modal-title"
            aria-describedby="transition-modal-description"
            className={classes.modal}
            open={open}
            onClose={handleClose}
            closeAfterTransition
            BackdropComponent={Backdrop}
            BackdropProps={{
                timeout: 500,
            }}
        >
            <Fade in={open}>
                <div className={classes.paper}>
                <Typography component="h1" variant="h5">
                {user ? 'Edit' : 'Add'}  User
                </Typography>
                <ValidatorForm className={classes.form} ref={form} noValidate onSubmit={submitUser}>
                    <Grid container spacing={2}>
                        <Grid item xs={12}>
                            <Input
                                name="name"
                                variant="outlined"
                                required
                                fullWidth
                                id="name"
                                label="Full Name"
                                placeholder="Full Name"
                                autoFocus
                                type="text"
                                value={formData.name}
                                handleChange={handleChange}
                                validators={['required']}
                                errorMessages={['this field is required']}
                            />
                        </Grid>                            
                        <Grid item xs={12}>
                            <Input
                                variant="outlined"
                                required
                                fullWidth
                                id="phone"
                                label="Phone Number"
                                placeholder="Phone Number"
                                name="phone"
                                value={formData.phone}
                                handleChange={handleChange}
                                validators={['required']}
                                errorMessages={['this field is required']}
                            />
                        </Grid>
                        <Grid item xs={12}>
                            <Input
                                variant="outlined"
                                {...(user ? {} : {required: true, validators: ['required'], errorMessages: ['this field is required']})}
                                fullWidth
                                name="password"
                                label="Password"
                                placeholder="Password"
                                type="password"
                                id="password"
                                value={formData.password}
                                handleChange={handleChange}
                            />
                        </Grid>
                        <Grid item xs={12}>
                            <FormControl variant="outlined" className={classes.formControl}>
                                <InputLabel htmlFor="outlined-role-native-simple">User Role</InputLabel>
                                <Select
                                    native
                                    value={formData.role}
                                    onChange={handleChange}
                                    label="Role"
                                    inputProps={{
                                        name: 'role',
                                        id: 'outlined-role-native-simple',
                                    }}
                                >
                                <option aria-label="None" value="">Select Role</option>
                                <option value="1">Super Admin</option>                              
                                <option value="2">Manager</option>                              
                                <option value="3">Dy. Manager</option>                              
                                <option value="4">Salesman</option>                              
                                </Select>
                            </FormControl>                            
                        </Grid>
                    </Grid>
                    <Button
                        type="submit"
                        fullWidth
                        variant="contained"
                        color="primary"
                        className={classes.submit}
                    >
                        Submit
                    </Button>
                </ValidatorForm>
                {error && 
                <Snackbar open={errorOpen} autoHideDuration={6000} onClose={() => {setErrorOpen(false); dispatch({ type: userConstants.RESET_RESPONSE});}}>
                    <Alert severity="error">{error.split(',').map((message, index) => {
                    return (<>{message}<br/></>);
                })}</Alert>
                </Snackbar>}  
                </div>
            </Fade>
        </Modal>
    )
}
export default AddUser;