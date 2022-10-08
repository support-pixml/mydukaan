import { Avatar, Box, Button, Collapse, Container, CssBaseline, Grid, IconButton, makeStyles, Paper, Snackbar, Table, TableBody, TableCell, TableContainer, TableFooter, TableHead, TablePagination, TableRow, Tooltip, Typography } from '@material-ui/core';
import React, { Fragment, useEffect, useState } from 'react';
import { BiCheckCircle, BiEditAlt, BiPrinter, BiTrashAlt } from 'react-icons/bi';
import { useDispatch, useSelector } from 'react-redux';
import { confirmOrder, deleteTempOrder, editTempOrder, getTempOrders } from '../../actions/orders';
import {IoIosArrowDown, IoIosArrowUp} from 'react-icons/io';
import AlertDialogue from '../../components/UI/DialogueBox';
import { Alert, AlertTitle } from '@material-ui/lab';
import { orderConstants } from '../../actions/constants';
import { Link } from 'react-router-dom'; 
import TempOrderButton from '../../components/UI/TempOrderButton';

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
    textField: {
        marginLeft: theme.spacing(1),
        marginRight: theme.spacing(1),
        width: 200,
    },
}));

function Row(props) {
    const { order, handleAlertOpen, handleAlertDeleteOpen, setErrorOpen } = props;
    const [open, setOpen] = useState(false);
    const [editOrder, setEditOrder] = useState();
    const dispatch = useDispatch();
    const auth = useSelector((state) => state.auth.authData);

    const handleEditOrder = () => {
        if(editOrder)
        dispatch(editTempOrder(editOrder));
        setEditOrder();   
        setErrorOpen(true);
    }

    return (
        <Fragment>
        <TableRow>
            <TableCell>
                <IconButton aria-label="expand row" size="small" onClick={() => setOpen(!open)}>
                    {open ? <IoIosArrowUp /> : <IoIosArrowDown />}
                </IconButton>
            </TableCell>
            <TableCell component="th" scope="row">
                {order.id}
            </TableCell>
            <TableCell>{order.company_name}</TableCell>
            <TableCell>
                {order.customer_phone ?  order.customer_phone : '--'}
            </TableCell>
            <TableCell>{order.orderby}</TableCell>
            <TableCell>{order.reference}</TableCell>
            <TableCell>{order.order_at}</TableCell>
            {(auth?.user.role === '1' || auth?.user.role === '2') && 
            <TableCell>
                {order.is_confirm === '0' && <Tooltip title="Confirm" aria-label="confirm" arrow>
                    <Button size="small" color="primary" variant="text" onClick={() => handleAlertOpen(order)}>
                        <BiCheckCircle />&nbsp;&nbsp;Confirm
                    </Button>
                </Tooltip>}
                &nbsp;&nbsp;               
                <Tooltip title="Delete" aria-label="delete" arrow>
                    <Button size="small" color="secondary" variant="text" onClick={() => handleAlertDeleteOpen(order)}>
                        <BiTrashAlt />&nbsp;&nbsp;Delete
                    </Button>
                </Tooltip>
                &nbsp;&nbsp;
                <Tooltip title="Print" aria-label="print" arrow>
                    <Link to={`/print_temp_order/${order.id}`} target="_blank">
                        <Button size="small" color="primary" variant="text">
                            <BiPrinter />&nbsp;&nbsp;Print
                        </Button>
                    </Link>
                </Tooltip>
            </TableCell>}
            {auth?.user.role === '3' && 
            <TableCell>
                <Tooltip title="Delete" aria-label="delete" arrow>
                    <Button size="small" color="secondary" variant="text" onClick={() => handleAlertDeleteOpen(order)}>
                        <BiTrashAlt />&nbsp;&nbsp;Delete
                    </Button>
                </Tooltip>
                &nbsp;&nbsp;
                <Tooltip title="Print" aria-label="print" arrow>
                    <Link to={`/print_temp_order/${order.id}`} target="_blank">
                        <Button size="small" color="primary" variant="text">
                            <BiPrinter />&nbsp;&nbsp;Print
                        </Button>
                    </Link>
                </Tooltip>
            </TableCell>}
            {auth?.user.role === '4' && 
            <TableCell>
                <Tooltip title="Print" aria-label="print" arrow>
                    <Link to={`/print_temp_order/${order.id}`} target="_blank">
                        <Button size="small" color="primary" variant="text">
                            <BiPrinter />&nbsp;&nbsp;Print
                        </Button>
                    </Link>
                </Tooltip>
            </TableCell>}
        </TableRow>
        <TableRow>
            <TableCell style={{ paddingBottom: 0, paddingTop: 0 }} colSpan={6}>
            <Collapse in={open} timeout="auto" unmountOnExit>
                <Grid container spacing={3}>
                    <Grid item xs={12} md={8}>
                        <Box margin={1}>
                            <Typography variant="h6" gutterBottom component="div">
                                Order Details
                            </Typography>
                            <Table size="small" aria-label="purchases">
                                <TableHead>
                                <TableRow>
                                    <TableCell>Category Name</TableCell>
                                    <TableCell>Product Image</TableCell>
                                    <TableCell>Product Name</TableCell>
                                    <TableCell>Product Description</TableCell>
                                    <TableCell>Price</TableCell>
                                    <TableCell>Quantity</TableCell>
                                </TableRow>
                                </TableHead>
                                <TableBody>
                                {order.order_details.map((detail) => (
                                    <TableRow key={detail.id}>
                                    <TableCell component="th" scope="row">
                                        {detail.category_name}
                                    </TableCell>
                                    <TableCell component="th" scope="row">
                                        <Avatar alt={detail.name} variant="rounded" src={`/uploads/products/${detail.image}`} />
                                    </TableCell>
                                    <TableCell component="th" scope="row">
                                        {detail.name}
                                    </TableCell>
                                    <TableCell component="th" scope="row">
                                        {detail.product_description}
                                    </TableCell>
                                    <TableCell>&#8377;{detail.price}</TableCell>
                                    <TableCell>
                                    {auth?.user.role !== '4' && order.is_confirm === '0' ? <TempOrderButton quantity={detail.quantity} order={order} detail={detail} setEditOrder={setEditOrder} /> : detail.quantity }
                                    </TableCell>
                                    </TableRow>
                                ))}
                                </TableBody>
                            </Table>
                            {order.note && 
                            <>
                            <Typography style={{ paddingTop: 10 }} variant="h6" component="div">
                                Order Note
                            </Typography>
                            {order.note}
                            </>}
                        </Box>
                    </Grid>
                    {auth?.user.role !== '4' &&
                    <Grid item xs={12} md={4}>
                        <Box margin={5}>
                        <Tooltip style={{ paddingTop: 10 }} title="Edit" aria-label="edit" arrow>
                            <Button size="small" color="primary" variant="outlined" onClick={() => handleEditOrder()}>
                                <BiEditAlt />&nbsp;&nbsp;Edit
                            </Button>
                        </Tooltip>                    
                        </Box>
                    </Grid>}
                </Grid>
            </Collapse>
            </TableCell>
        </TableRow>
        </Fragment>
    )
}

const TempOrders = () => {
    const classes = useStyles();
    const dispatch = useDispatch();
    const [page, setPage] = useState(0);
    const [rowsPerPage, setRowsPerPage] = useState(10);
    const [alertOpen, setAlertOpen] = useState(false);
    const [alertDeleteOpen, setAlertDeleteOpen] = useState(false);
    const [orderId, setOrderId] = useState(null);
    const [errorOpen, setErrorOpen] = useState(true);

    const alertTitle = 'Confirm?';
    const alertMessage = 'Are you sure to confirm this order?';

    const alertDeleteTitle = 'Confirm?';
    const alertDeleteMessage = 'Are you sure to delete this order?';

    useEffect(() => {
        dispatch(getTempOrders());
    }, []);

    const orders = useSelector((state) => state.order.orders);
    const message = useSelector((state) => state.order.message);
    const auth = useSelector((state) => state.auth.authData);

    const emptyRows = rowsPerPage - Math.min(rowsPerPage, orders.length - page * rowsPerPage);

    const handleChangePage = (event, newPage) => {
        setPage(newPage);
    };

    const handleChangeRowsPerPage = (event) => {
        setRowsPerPage(parseInt(event.target.value, 10));
        setPage(0);
    };

    const handleConfirm = (order_id) => {
        setAlertOpen(false);    
        dispatch(confirmOrder(order_id));
    }

    const handleDelete = (order_id) => {
        dispatch(deleteTempOrder(order_id));
        setAlertDeleteOpen(false);    
    }

    const handleAlertOpen = (order) => {
        setAlertOpen(true);
        setOrderId(order.id);
        setErrorOpen(true);
    };

    const handleAlertDeleteOpen = (order) => {
        setAlertDeleteOpen(true);
        setOrderId(order.id);
        setErrorOpen(true);
    };
    
    const handleAlertClose = (order_id) => {
        setAlertOpen(false);
        setOrderId(null);
    };

    const handleAlertDeleteClose = (order_id) => {
        setAlertDeleteOpen(false);
        setOrderId(null);
    };

    const errorCloseHandler = () => {
        setErrorOpen(false);
        dispatch({
            type: orderConstants.CLEAR_MESSAGE
        });
    }

    const renderOrdersTable = () => {
        return (
            <TableContainer component={Paper}>
                <Table className={classes.table} aria-label="simple table">
                    <TableHead>
                    <TableRow>
                        <TableCell />
                        <TableCell>Order No</TableCell>
                        <TableCell>Company Name</TableCell>
                        <TableCell>Phone</TableCell>
                        <TableCell>Order By</TableCell>
                        <TableCell>Order Ref</TableCell>
                        <TableCell>Order At</TableCell>
                        <TableCell>Action</TableCell>
                    </TableRow>
                    </TableHead>
                    <TableBody>
                    {(rowsPerPage > 0
                        ? orders.slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage)
                        : orders
                        ).map((order) => {
                        return (                           
                            <Row key={order.id} order={order} handleAlertOpen={handleAlertOpen} handleAlertDeleteOpen={handleAlertDeleteOpen} setErrorOpen={setErrorOpen} />    
                        );
                    })}
                    {emptyRows > 0 && (
                    <TableRow style={{ height: 53 * emptyRows }}>
                        <TableCell colSpan={6} />
                    </TableRow>
                    )}
                    </TableBody>
                    <TableFooter>
                        <TableRow>
                            <TablePagination
                            rowsPerPageOptions={[10, 25, { label: 'All', value: -1 }]}
                            colSpan={3}
                            count={orders.length}
                            rowsPerPage={rowsPerPage}
                            page={page}
                            SelectProps={{
                                inputProps: { 'aria-label': 'rows per page' },
                                native: true,
                            }}
                            onChangePage={handleChangePage}
                            onChangeRowsPerPage={handleChangeRowsPerPage}
                            />
                        </TableRow>
                    </TableFooter>
                </Table>
            </TableContainer>
        )
    } 

    return (
        <Container component="main" maxWidth="lg">
            <CssBaseline />
            <div className={classes.paper}>
                <Grid container spacing={2}>
                    <Grid item xs={12} md={12}>
                        <Typography variant="h4" gutterBottom component="h5">
                            Salesman's Orders
                        </Typography>
                    </Grid>
                    {new Date(customer.expiry_date).getTime() < new Date().getTime() &&
                    <Grid item xs={12} md={12}>
                        <Alert severity="warning">
                            <AlertTitle>Warning</AlertTitle>
                            Your account has been expired. — <strong>Your Expiry Date: {customer.expiry_date}</strong>
                        </Alert>
                    </Grid>}
                    {customer && new Date(customer.expiry_date).getFullYear() == new Date().getFullYear() && new Date(customer.expiry_date).getMonth() == new Date().getMonth() && new Date(customer.expiry_date).getDate() == new Date().getDate()+2 &&
                    <Grid item xs={12} md={12}>
                        <Alert severity="warning" className={classes.alert}>
                            <AlertTitle>Warning</AlertTitle>
                            Your account will be going to expire in 2 days. — <strong>Your Expiry Date: {customer.expiry_date}</strong>
                        </Alert>
                    </Grid>}
                    {customer && new Date(customer.expiry_date).getFullYear() == new Date().getFullYear() && new Date(customer.expiry_date).getMonth() == new Date().getMonth() && new Date(customer.expiry_date).getDate() == new Date().getDate()+1 &&
                    <Grid item xs={12} md={12}>
                        <Alert severity="warning" className={classes.alert}>
                            <AlertTitle>Warning</AlertTitle>
                            Your account will be going to expire tomorrow. — <strong>Your Expiry Date: {customer.expiry_date}</strong>
                        </Alert>
                    </Grid>}
                    <Grid item xs={12} md={12}>
                        {renderOrdersTable()}
                        <AlertDialogue open={alertOpen} handleClose={() => handleAlertClose()} title={alertTitle} message={alertMessage} handleAction={() => handleConfirm(orderId)} />
                        <AlertDialogue open={alertDeleteOpen} handleClose={() => handleAlertDeleteClose()} title={alertDeleteTitle} message={alertDeleteMessage} handleAction={() => handleDelete(orderId)} />
                        {message && 
                        <Snackbar open={errorOpen} autoHideDuration={6000} onClose={errorCloseHandler}>
                            <Alert severity="success">{message.message}</Alert>
                        </Snackbar>}  
                    </Grid>
                </Grid>
            </div>
        </Container>
    )
}

export default TempOrders;