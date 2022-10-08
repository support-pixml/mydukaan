import { Button, Container, CssBaseline, Grid, makeStyles, Paper, Snackbar, Table, TableBody, TableCell, TableContainer, TableHead, TableRow } from '@material-ui/core';
import React, { useEffect, useState } from 'react';
import { Redirect } from 'react-router-dom';
import {useDispatch, useSelector} from 'react-redux';
import { getUsers, removeUser } from '../../actions/users';
import AddUser from './addUser';
import { BiEditAlt, BiTrashAlt } from 'react-icons/bi';
import AlertDialogue from '../../components/UI/DialogueBox';
import { Alert, AlertTitle } from '@material-ui/lab';
import { userConstants } from '../../actions/constants';

const useStyles = makeStyles((theme) => ({
    paper: {
        marginTop: theme.spacing(15),
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
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
    selectEmpty: {
        marginTop: theme.spacing(2),
    },
    table: {
        display: 'block',
        width: '100%',
        overflowX: 'auto',
    },
}));

const Users = () => {
    const classes = useStyles();
    const dispatch = useDispatch();
    const [userOpen, setUserOpen] = useState(false);
    const [alertOpen, setAlertOpen] = useState(false);
    const [userId, setUserId] = useState(false);
    const [user, setUser] = useState(null);
    const alertTitle = 'Confirm?';
    const alertMessage = 'Are you sure to delete this category?';
    const [messageOpen, setMessageOpen] = useState(true);

    useEffect(() => {
        dispatch(getUsers());
    }, []);

    const users = useSelector((state) => state.user.users);
    const message = useSelector(state => state.user.message);
    const auth = useSelector((state) => state.auth.authData);
    if(auth?.user.role !== '1')
    {
        return <Redirect to={`/`} />
    }

    const handleUserOpen = () => {
        setUserOpen(true);
        setMessageOpen(false);
        setUser(null);
    };

    const handleUserClose = () => {
        setUserOpen(false);
        setUser(null); 
        setUserId(null);
    };

    const handleDelete = () => {
        dispatch(removeUser(userId));  
        setAlertOpen(false);    
    }

    const handleUpdate = (user) => {
        setUserOpen(true);  
        setUser(user);       
    }

    const handleAlertOpen = (user) => {
        setAlertOpen(true);
        setUserId(user.long_id);
    };
    
    const handleAlertClose = () => {
        setAlertOpen(false);
        setUserId(null);
    };

    useEffect(() => {
        if(message)
        {
            setUserOpen(false);
            setMessageOpen(true);
            setUserId(null);
        }
    }, [message]);
    

    const renderUsersTable = () => {
        return (
            <TableContainer component={Paper}>
                <Table className={classes.table} aria-label="simple table">
                    <TableHead>
                    <TableRow>
                        <TableCell>Name</TableCell>
                        <TableCell>Phone</TableCell>
                        <TableCell>Role</TableCell>
                        <TableCell align="right">Action</TableCell>
                    </TableRow>
                    </TableHead>
                    <TableBody>
                    {users.map((user, index) => {
                        return (
                        <TableRow key={index}>
                            <TableCell component="td" scope="row">
                                {user.name}
                            </TableCell>
                            <TableCell component="td" scope="row">
                                {user.phone}
                            </TableCell>
                            <TableCell component="td" scope="row">
                                {user.role === '1' && 'Admin'}
                                {user.role === '2' && 'Manager'}
                                {user.role === '3' && 'Dy. Manager'}
                                {user.role === '4' && 'Salesman'}
                            </TableCell>
                            <TableCell style={{ width: 160 }} align="right">
                                <BiEditAlt onClick={() => handleUpdate(user)} /> | <BiTrashAlt onClick={() => handleAlertOpen(user)} />
                            </TableCell>
                        </TableRow>
                        )
                    })}
                    </TableBody>
                </Table>
            </TableContainer>
        )
    }

    return (
        <Container component="main" maxWidth="lg">
            <CssBaseline />
            <div className={classes.paper}>
            <Grid container spacing={2}>
                {new Date(customer.expiry_date).getTime() < new Date().getTime() &&
                <Grid item xs={12} md={12}>
                    <Alert severity="warning">
                        <AlertTitle>Warning</AlertTitle>
                        Your account has been expired. — <strong>Your Expiry Date: {customer.expiry_date}</strong>
                    </Alert>
                </Grid>}
                {customer && new Date(customer.expiry_date).getFullYear() == new Date().getFullYear() && new Date(customer.expiry_date).getMonth() == new Date().getMonth() && new Date(customer.expiry_date).getDate() == new Date().getDate()+2 &&
                <Alert severity="warning" className={classes.alert}>
                    <AlertTitle>Warning</AlertTitle>
                    Your account will be going to expire in 2 days. — <strong>Your Expiry Date: {customer.expiry_date}</strong>
                </Alert>}
                {customer && new Date(customer.expiry_date).getFullYear() == new Date().getFullYear() && new Date(customer.expiry_date).getMonth() == new Date().getMonth() && new Date(customer.expiry_date).getDate() == new Date().getDate()+1 &&
                <Alert severity="warning" className={classes.alert}>
                    <AlertTitle>Warning</AlertTitle>
                    Your account will be going to expire tomorrow. — <strong>Your Expiry Date: {customer.expiry_date}</strong>
                </Alert>}
                <Grid item xs={12} md={12}>
                    <Button
                        type="submit"
                        variant="contained"
                        color="primary"
                        className="float-right mb-2"
                        onClick={handleUserOpen}
                    >
                        Add User
                    </Button>
                    <AddUser handleClose={handleUserClose} open={userOpen} user={user} />
                    {renderUsersTable()}
                    <AlertDialogue open={alertOpen} handleClose={() => handleAlertClose(userId)} title={alertTitle} message={alertMessage} handleAction={() => handleDelete()} />
                </Grid> 
                {message && 
                <Snackbar open={messageOpen} autoHideDuration={6000} onClose={() => {setMessageOpen(false); dispatch({ type: userConstants.RESET_RESPONSE});}}>
                    <Alert severity="success">{message.message}</Alert>
                </Snackbar>}                 
            </Grid>
            </div>
        </Container>
    )
}

export default Users;