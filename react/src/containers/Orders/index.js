import { Avatar, Box, Button, Collapse, Container, CssBaseline, FormControl, Grid, IconButton, makeStyles, Paper, Snackbar, Table, TableBody, TableCell, TableContainer, TableFooter, TableHead, TablePagination, TableRow, TextField, Tooltip, Typography } from '@material-ui/core';
import React, { Fragment, useEffect, useState } from 'react';
import { BiCheckCircle, BiEditAlt, BiPrinter, BiTrashAlt } from 'react-icons/bi';
import { useDispatch, useSelector } from 'react-redux';
import { deleteOrder, getAllOrders, exportOrders, editReserveOrder } from '../../actions/orders';
import {IoIosArrowDown, IoIosArrowUp, IoLogoWhatsapp} from 'react-icons/io';
import AlertDialogue from '../../components/UI/DialogueBox';
import { Alert, AlertTitle, Autocomplete } from '@material-ui/lab';
import { Redirect } from 'react-router';
import { CSVLink } from 'react-csv';
import { getUsers } from '../../actions/users';
import { getProducts } from '../../actions/products';
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
    const { order, handleAlertOpen, setErrorOpen } = props;
    const [open, setOpen] = useState(false);
    const [editOrder, setEditOrder] = useState();
    const dispatch = useDispatch();
    const auth = useSelector((state) => state.auth.authData);

    const handleEditOrder = () => {
        if(editOrder)
        dispatch(editReserveOrder(editOrder));
        setEditOrder();   
        setErrorOpen(true);
    }

    if(auth?.user.role === '3')
    {
        return <Redirect to={`/`} />
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
            <TableCell>{order.customer_company}</TableCell>
            <TableCell>{order.customer_email ? order.customer_email : "--"}</TableCell>
            <TableCell>
                {order.customer_phone}
            </TableCell>
            <TableCell>&#8377;{order.order_total}</TableCell>
            <TableCell>{order.order_by}</TableCell>
            <TableCell>{order.ref_name}</TableCell>
            <TableCell>{order.created_at}</TableCell>
            <TableCell>{order.is_reserve == 1 ? 'Yes' : 'No'}</TableCell>
            <TableCell>
                <Tooltip title="Delete" aria-label="delete" arrow>
                    <Button size="small" color="secondary" variant="text" onClick={() => handleAlertOpen(order)}>
                        <BiTrashAlt />&nbsp;&nbsp;Delete
                    </Button>
                </Tooltip>
                <Tooltip title="Print" aria-label="print" arrow>
                    <Link to={`/print_order/${order.long_id}`} target="_blank">
                        <Button size="small" color="primary" variant="text">
                            <BiPrinter />&nbsp;&nbsp;Print
                        </Button>
                    </Link>
                </Tooltip>
                {order.customer_phone && <Tooltip title="Whatsapp" aria-label="whatsapp" arrow>
                    <a href={`https://api.whatsapp.com/send?phone=91${order.customer_phone}`} target="_blank">
                        <Button size="small" variant="text">
                            <IoLogoWhatsapp />&nbsp;&nbsp;Whatsapp
                        </Button>
                    </a>
                </Tooltip>}
            </TableCell>
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
                                        <Avatar alt={detail.product_name} variant="rounded" src={`/uploads/products/${detail.image}`} />
                                    </TableCell>
                                    <TableCell component="th" scope="row">
                                        {detail.product_name}
                                    </TableCell>
                                    <TableCell component="th" scope="row">
                                        {detail.product_description}
                                    </TableCell>
                                    <TableCell>&#8377;{detail.product_price}</TableCell>
                                    <TableCell>
                                    {order.is_reserve == 1 ? <TempOrderButton quantity={detail.quantity} order={order} detail={detail} setEditOrder={setEditOrder} /> : detail.quantity }
                                    </TableCell>
                                    </TableRow>
                                ))}
                                </TableBody>
                            </Table>
                        </Box>
                    </Grid>
                    
                    {order.is_reserve == 1 && <Grid item xs={12} md={4}>
                        <Box margin={1}>
                        <Tooltip style={{ paddingTop: 5 }} title="Edit" aria-label="edit" arrow>
                            <Button size="small" color="primary" variant="outlined" onClick={() => handleEditOrder()}>
                                <BiEditAlt />&nbsp;&nbsp;Edit
                            </Button>
                        </Tooltip>                    
                        <Tooltip style={{ paddingTop: 5, marginLeft: 10 }} title="Confirm Order" aria-label="confirm" arrow>
                            <Button size="small" color="primary" variant="outlined" onClick={() => handleEditOrder()}>
                                <BiCheckCircle />&nbsp;&nbsp;Confirm
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

const Orders = () => {
    const classes = useStyles();
    const dispatch = useDispatch();
    const [page, setPage] = useState(0);
    const [rowsPerPage, setRowsPerPage] = useState(10);
    const [alertOpen, setAlertOpen] = useState(false);
    const [orderId, setOrderId] = useState(null);
    const [errorOpen, setErrorOpen] = useState(true);
    const [companyName, setCompanyName] = useState();
    const [reference, setReference] = useState();
    const [fromDate, setFromDate] = useState();
    const [toDate, setToDate] = useState();
    const [selectProduct, setSelectProduct] = useState();

    const alertTitle = 'Confirm?';
    const alertMessage = 'Are you sure to delete this order?';

    useEffect(() => {
        dispatch(getAllOrders());
        dispatch(getUsers());
        dispatch(getProducts());
    }, []);
    
    useEffect(() => {
        dispatch(exportOrders({reference: reference, fromDate: fromDate, toDate: toDate, product_id: selectProduct, companyName}));
        dispatch(getAllOrders({reference: reference, fromDate: fromDate, toDate: toDate, product_id: selectProduct, companyName}));
    }, [reference, fromDate, toDate, selectProduct, companyName]);

    const orders = useSelector((state) => state.order.orders);
    const message = useSelector((state) => state.order.message);
    const users = useSelector((state) => state.user.users);
    const products = useSelector((state) => state.product.products_list);
    
    const emptyRows = rowsPerPage - Math.min(rowsPerPage, orders.length - page * rowsPerPage);

    const export_orders = useSelector((state) => state.order.exports);

    const handleChangePage = (event, newPage) => {
        setPage(newPage);
    };

    const handleChangeRowsPerPage = (event) => {
        setRowsPerPage(parseInt(event.target.value, 10));
        setPage(0);
    };

    const handleDelete = (long_id) => {
        dispatch(deleteOrder(long_id));
        setAlertOpen(false);    
    }

    const handleAlertOpen = (order) => {
        setAlertOpen(true);
        setOrderId(order.long_id);
        setErrorOpen(true);
    };
    
    const handleAlertClose = (order_id) => {
        setAlertOpen(false);
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
                        <TableCell>Email</TableCell>
                        <TableCell>Phone</TableCell>
                        <TableCell>Order Total</TableCell>
                        <TableCell>Order By</TableCell>
                        <TableCell>Order Ref</TableCell>
                        <TableCell>Order At</TableCell>
                        <TableCell>Reserved</TableCell>
                        <TableCell>Action</TableCell>
                    </TableRow>
                    </TableHead>
                    <TableBody>
                    {(rowsPerPage > 0
                        ? orders.slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage)
                        : orders
                        ).map((order) => {
                        return (                           
                            <Row key={order.long_id} order={order} handleAlertOpen={handleAlertOpen} setErrorOpen={setErrorOpen}/>    
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
                            Orders
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

                    <Grid item xs={12} md={2}>
                        <FormControl variant="outlined" className={classes.formControl}>
                            <TextField
                                id="company_name"
                                label="Company Name"
                                type="text"
                                onChange={(e) => setCompanyName(e.target.value)}
                                className={classes.textField}
                                InputLabelProps={{
                                shrink: true,
                                }}
                            />
                        </FormControl>
                    </Grid>                    
                    <Grid item xs={12} md={2}>
                        <FormControl variant="outlined" className={classes.formControl}>
                            <Autocomplete
                                id="select-reference"
                                options={users}
                                getOptionLabel={(user) => user.name}
                                onChange={(event, newValue) => {
                                    if(newValue)
                                    {
                                        setReference(newValue.id);                                  
                                    }
                                    else
                                    {
                                        setReference();
                                    }
                                }}
                                renderInput={(params) => <TextField {...params} className={classes.textField} label="Select User" variant="outlined" />}
                            />
                        </FormControl>
                    </Grid>
                    <Grid item xs={12} md={2}>
                        <FormControl variant="outlined" className={classes.formControl}>
                            <TextField
                                id="from"
                                label="From"
                                type="date"
                                onChange={(e) => setFromDate(e.target.value)}
                                className={classes.textField}
                                InputLabelProps={{
                                shrink: true,
                                }}
                            />
                        </FormControl>
                    </Grid>
                    <Grid item xs={12} md={2}>
                        <FormControl variant="outlined" className={classes.formControl}>
                            <TextField
                                id="to"
                                label="To"
                                type="date"
                                onChange={(e) => setToDate(e.target.value)}
                                className={classes.textField}
                                InputLabelProps={{
                                shrink: true,
                                }}
                            />
                        </FormControl>
                    </Grid>
                    <Grid item xs={12} md={2}>
                        <FormControl variant="outlined" className={`${classes.formControl}`}>
                            <Autocomplete
                                id="select-product"
                                options={products}
                                getOptionLabel={(product) => product.name}
                                onChange={(event, newValue) => {
                                    if(newValue)
                                    {
                                        setSelectProduct(newValue.id);                                   
                                    }
                                    else
                                    {
                                        setSelectProduct();
                                    }
                                }}
                                renderInput={(params) => <TextField {...params} className={classes.textField} label="Select Products" variant="outlined" />}
                                />
                        </FormControl>
                    </Grid>
                    <Grid item xs={12} md={2}>
                        {export_orders &&
                        <CSVLink data={export_orders} 
                            className="ml-2 mt-2 btn btn-block btn-primary"
                            target="_blank"
                            filename="orders.csv">Export Orders</CSVLink>}
                    </Grid>
                    <Grid item xs={12} md={12}>
                        {renderOrdersTable()}
                        <AlertDialogue open={alertOpen} handleClose={() => handleAlertClose()} title={alertTitle} message={alertMessage} handleAction={() => handleDelete(orderId)} />
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

export default Orders;