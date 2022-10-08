import { Avatar, Button, Container, CssBaseline, Grid, InputBase, makeStyles, Paper, Snackbar, Table, TableBody, TableCell, TableContainer, TableFooter, TableHead, TablePagination, TableRow } from '@material-ui/core';
import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { exportProducts, getProducts, importProducts, deleteProduct, demoExportProducts} from '../../actions/products';
import AddProduct from './addProduct';
import { CSVLink } from 'react-csv';
import { Alert, AlertTitle } from '@material-ui/lab';
import AddProductStock from './addProductStock';
import AlertDialogue from '../../components/UI/DialogueBox';
import { errorConstants, productConstants } from '../../actions/constants';
import ShowProductStocks from "./showProductStocks";
import DeleteIcon from '@material-ui/icons/DeleteOutlined';
import EditIcon from '@material-ui/icons/EditOutlined';
import ListIcon from '@material-ui/icons/ListAltOutlined';
import AddIcon from '@material-ui/icons/Add';

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
    inputRoot: {
        color: 'inherit',
    },
    inputInput: {
        // padding: theme.spacing(1, 1, 1, 1),
        // vertical padding + font size from searchIcon
        paddingLeft: '10px',
        // transition: theme.transitions.create('width'),
        width: '100%',
        border: '1px solid lightgray',
        [theme.breakpoints.up('md')]: {
            width: '20ch',
        },
    },
}));

const Products = () => {
    const classes = useStyles();
    const [page, setPage] = useState(0);
    const [products, setProducts] = useState([]);
    const [rowsPerPage, setRowsPerPage] = useState(10);
    const [expanded, setExpanded] = useState('');
    const dispatch = useDispatch();
    const [currentId, setCurrentId] = useState(0);     
    const [productId, setProductId] = useState(0);     
    const [alertOpen, setAlertOpen] = useState(false);
    const [errorOpen, setErrorOpen] = useState(true);
    const [messageOpen, setMessageOpen] = useState(true);
    const [stockModalOpen, setStockModalOpen] = useState(false);
    const [productStocksModalOpen, setProductStocksModalOpen] = useState(false);
    const alertTitle = 'Confirm?';
    const alertMessage = 'Are you sure to delete this product?';
    const form = new FormData();

    let allProducts = useSelector((state) => state.product.products_list);
    console.log('all products', allProducts);
    useEffect(() => {
        console.log('use effect', allProducts);
        if(allProducts.length == 0)
        {
            dispatch(getProducts());
            dispatch(exportProducts());
            dispatch(demoExportProducts());
        }
        else
        {
            setProducts(allProducts);
        }
    }, [allProducts]);
    
    
    const error = useSelector(state => state.error.error);
    const message = useSelector(state => state.product.message);

    // if(message !== null)
    // {
    //     window.location.reload();
    // }   
    
    const selected_product = useSelector((state) => (currentId ? state.product.products_list.find((product) => product.long_id === currentId) : null));
    
    const export_products = useSelector((state) => state.product.exports);
    const import_template_data = useSelector((state) => state.product.demoExports);

    const auth = useSelector((state) => state.auth.authData);

    const emptyRows = rowsPerPage - Math.min(rowsPerPage, products ? products.length : 0 - page * rowsPerPage);

    const handleChangePage = (event, newPage) => {
        setPage(newPage);
    };

    const handleChangeRowsPerPage = (event) => {
        setRowsPerPage(parseInt(event.target.value, 10));
        setPage(0);
    };

    const handleAccordion = () => {
        setCurrentId(null);
        if(expanded === '')
            setExpanded('panel1');
        else
            setExpanded('');
    }

    const messageHandler = () => {
        setErrorOpen(true);
        setMessageOpen(true);
    }

    const handleUpdate = (product_long_id) => {
        setCurrentId(product_long_id);
        setExpanded('panel1');
        window[`scrollTo`]({top: 0, behavior: 'smooth'})
    }

    const handleImport = (e) => {
        form.append('csv', e.target.files[0]);
        dispatch(importProducts(form));
        form.append('csv', '');
        setErrorOpen(true);
        setMessageOpen(true);
    }

    const handleStockModalOpen = (product_long_id) => {
        setStockModalOpen(true);
        setCurrentId(product_long_id);
        setExpanded('');
    }

    const handleProductStocksModalOpen = (product_long_id) => {
        setProductStocksModalOpen(true);
        setCurrentId(product_long_id);
        setExpanded('');
    }
    
    const handleStockModalClose = () => {
        setStockModalOpen(false);
        setCurrentId(null);
    };

    const handleProductStockModalClose = () => {
        setProductStocksModalOpen(false);
        setCurrentId(null);
    };

    const handleAlertOpen = (product_long_id) => {
        setAlertOpen(true);
        setProductId(product_long_id);
        setMessageOpen(true);
    };

    const handleAlertClose = (product_long_id) => {
        setAlertOpen(false);
        setProductId(0);
        setMessageOpen(true);
    };

    const handleDelete = (product_long_id) => {
        dispatch(deleteProduct(product_long_id));
        setAlertOpen(false);    
    }

    const errorCloseHandler = () => {
        setErrorOpen(false);
        setMessageOpen(false);
        dispatch({
            type: errorConstants.ERROR_CLEAR
        });
        dispatch({
            type: productConstants.CLEAR_MESSAGE
        });
    }

    const handleProductSearch = (productSearch) => {
        if(productSearch)
        {
            const searchProducts = products.filter(product => product.name.toLowerCase().includes(productSearch.toLowerCase()));
            setProducts(searchProducts);
        }       
        else
        {
            setProducts(allProducts);
        }
    }

    const renderProductsTable = () => {
        return (
            <TableContainer component={Paper}>
                <Table className={classes.table} aria-label="simple table">
                    <TableHead>
                    <TableRow>
                        <TableCell>Image</TableCell>
                        <TableCell>Product Name</TableCell>
                        <TableCell>Category</TableCell>
                        <TableCell>Price</TableCell>
                        <TableCell>M.S.Q.</TableCell>
                        <TableCell>Stock</TableCell>
                        {(auth?.user.role === '1' || auth?.user.role === '2') ? 
                        <TableCell>Action</TableCell> : ''}
                    </TableRow>
                    </TableHead>
                    <TableBody>
                    {(rowsPerPage > 0
                        ? products.slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage)
                        : products
                    ).map((product) => {
                        return (
                        <TableRow key={product.long_id}>
                        <TableCell>
                            <Avatar alt={product.name} variant="rounded" src={`/uploads/products/${product.image}`} />
                        </TableCell>
                        <TableCell component="th" scope="row">
                            {product.name}
                        </TableCell>
                        <TableCell>{product.cat_name}</TableCell>
                        <TableCell>&#8377;{product.price}</TableCell>
                        <TableCell>{product.min_stock_qty}</TableCell>
                        <TableCell>{product.total_stock}</TableCell>
                        {(auth?.user.role === '1' || auth?.user.role === '2') ? 
                        <TableCell>
                            <Button size="small" color="default" variant="outlined" className="mr-2" onClick={() => handleStockModalOpen(product.long_id)} startIcon={<AddIcon />}>
                                Add Stock
                            </Button>                          
                            <Button size="small" color="default" variant="outlined" className="mr-2" onClick={() => handleProductStocksModalOpen(product.long_id)} startIcon={<ListIcon />}>
                                Check Stocks
                            </Button>                          
                            <Button size="small" color="primary" variant="outlined" className="mr-2" onClick={() => handleUpdate(product.long_id)} startIcon={<EditIcon />}>
                                Edit
                            </Button> 
                            <Button size="small" color="secondary" variant="outlined" onClick={() => handleAlertOpen(product.long_id)} startIcon={<DeleteIcon />}>
                                Delete
                            </Button>                            
                        </TableCell> : ""}
                        </TableRow>
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
                        rowsPerPageOptions={[5, 10, 25, { label: 'All', value: -1 }]}
                        colSpan={3}
                        count={products.length}
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
                {customer.total_products <= products.length &&
                <Grid item xs={12} md={12}>
                    <Alert severity="warning">
                        <AlertTitle>Warning</AlertTitle>
                        You have reached to your total products allotment. — <strong>Your Total Products: {customer.total_products}</strong>
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
                {new Date(customer.expiry_date).getTime() < new Date().getTime() &&
                <Grid item xs={12} md={12}>
                    <Alert severity="warning">
                        <AlertTitle>Warning</AlertTitle>
                        Your account has been expired. — <strong>Your Expiry Date: {customer.expiry_date}</strong>
                    </Alert>
                </Grid>}
               
                {(auth?.user.role === '1' || auth?.user.role === '2') ? 
                <Grid item xs={12} md={12}>                    
                    <Button
                        variant="contained"
                        className="mt-2"
                        color="primary"
                        onClick={handleAccordion}
                    >
                    Add Product
                    </Button>
                    <Button
                        variant="outlined"
                        className="ml-2 mt-2"
                        component="label"                        
                        color="secondary"
                        >
                        Import Products
                        <input
                            type="file"
                            accept=".csv"
                            onChange={handleImport}
                            hidden
                        />
                    </Button>
                    {export_products &&
                    <CSVLink data={export_products} 
                        className="ml-2 mt-2 btn btn-primary"
                        target="_blank"
                        filename="products.csv">Export Products</CSVLink>}
                    {import_template_data && <CSVLink data={import_template_data} 
                        className="ml-2 mt-2 btn btn-info"
                        target="_blank"
                        filename="import_template.csv">Download Import Template</CSVLink>}
                    <InputBase
                        placeholder="Search…"
                        classes={{
                            input: classes.inputInput,
                        }}
                        style={{top: `4px`}}
                        className="ml-2 mt-2"
                        inputProps={{ 'aria-label': 'search' }}
                        onChange={(e) => handleProductSearch(e.target.value.trim())}
                    />
                </Grid> : ''}
                {(auth?.user.role === '1' || auth?.user.role === '2') ? 
                <Grid item xs={12} md={12}>
                    <AddProduct expanded={expanded} setExpanded={setExpanded} product={selected_product} message={message} onMessageHandler={messageHandler}/>
                </Grid> : ''}
                <Grid item xs={12} md={12}>
                    {renderProductsTable()}
                    <AddProductStock handleClose={handleStockModalClose} open={stockModalOpen} product={selected_product}/>
                    <ShowProductStocks handleClose={handleProductStockModalClose} open={productStocksModalOpen} product={selected_product}/>
                </Grid>
            </Grid>
            <AlertDialogue open={alertOpen} handleClose={() => handleAlertClose()} title={alertTitle} message={alertMessage} handleAction={() => handleDelete(productId)} />
            {error && 
            <Snackbar open={errorOpen} autoHideDuration={3000} onClose={errorCloseHandler}>
                <Alert severity="error">
                    {error.split(',').map((message) => {
                        return (<>{message}<br/></>);
                    })}
                </Alert>
            </Snackbar>} 
            {message && 
            <Snackbar open={messageOpen} autoHideDuration={3000} onClose={errorCloseHandler}>
                <Alert severity="success">{message.message}</Alert>
            </Snackbar>} 
            </div>
        </Container>
    )
}

export default Products;