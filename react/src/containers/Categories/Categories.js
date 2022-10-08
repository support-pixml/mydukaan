import { Button, Container, Grid, makeStyles, Paper, Snackbar, Table, TableBody, TableCell, TableContainer, TableFooter, TableHead, TablePagination, TableRow, withStyles} from '@material-ui/core';
import React, { useEffect, useState } from 'react';
import {useDispatch, useSelector} from 'react-redux';
import { deleteCategory, getCategories } from '../../actions/categories';
import AddCategory from './addCategory';
import { BiTrashAlt, BiEditAlt } from 'react-icons/bi';
import AlertDialogue from '../../components/UI/DialogueBox';
import { Alert } from '@material-ui/lab';
import { categoryConstants } from '../../actions/constants';

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
}));

const ShowCategories = () => {
    const classes = useStyles();
    const dispatch = useDispatch();
    const [categoryOpen, setCategoryOpen] = useState(false);
    const [alertOpen, setAlertOpen] = useState(false);
    const [categoryId, setCategoryId] = useState(null);
    const [category, setCategory] = useState(null);
    const [page, setPage] = useState(0);
    const [rowsPerPage, setRowsPerPage] = useState(5);
    const alertTitle = 'Confirm?';
    const alertMessage = 'Are you sure to delete this category?';
    const [messageOpen, setMessageOpen] = useState(true);
    const [errorOpen, setErrorOpen] = useState(true);
    
    useEffect(() => {
        dispatch(getCategories());
    }, []);

    const auth = useSelector((state) => state.auth.authData);
    if(auth?.user.role === '3')
    {
        return <Redirect to={`/`} />
    }
    
    const categories = useSelector((state) => state.category.categories);
    const message = useSelector((state) => state.category.message);
    const error = useSelector(state => state.error.error);
    
    const emptyRows = rowsPerPage - Math.min(rowsPerPage, categories.length - page * rowsPerPage);

    const handleCategoryOpen = () => {
        setCategoryOpen(true);
        setMessageOpen(true);
    };

    const handleCategoryClose = () => {
        setCategoryOpen(false);
        setCategoryId(null);
        setCategory(null);
        setMessageOpen(true);
    };

    const handleAlertOpen = (category) => {
        setAlertOpen(true);
        setCategoryId(category.long_id);
        setMessageOpen(true);
    };
    
    const handleAlertClose = (category_id) => {
        setAlertOpen(false);
        setCategoryId(null);
        setMessageOpen(true);
    };

    const handleChangePage = (event, newPage) => {
        setPage(newPage);
    };

    const handleChangeRowsPerPage = (event) => {
        setRowsPerPage(parseInt(event.target.value, 10));
        setPage(0);
    };

    const handleDelete = (long_id) => {
        dispatch(deleteCategory(long_id));
        setAlertOpen(false);    
    }

    const handleUpdate = (category) => {
        setCategoryOpen(true); 
        setCategory(category);    
    }

    const StyledTableCell = withStyles((theme) => ({
    head: {
        backgroundColor: theme.palette.common.black,
        color: theme.palette.common.white,
    },
    body: {
        fontSize: 14,
    },
    }))(TableCell);

    const renderCategoriesTable = () => {
    return (
        <TableContainer component={Paper}>
            <Table className={classes.table} aria-label="custom pagination table">
                <TableHead>
                    <TableRow>
                        <StyledTableCell>Category</StyledTableCell>
                        <StyledTableCell align="right">Action</StyledTableCell>
                    </TableRow>
                </TableHead>          
                <TableBody>
                {(rowsPerPage > 0
                    ? categories.slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage)
                    : categories
                ).map((row, index) => {
                    return (
                    <TableRow key={index}>
                        <TableCell component="th" scope="row">
                            {row.name}
                        </TableCell>
                        <TableCell style={{ width: 160 }} align="right">
                            <BiEditAlt onClick={() => handleUpdate(row)}/> | 
                            <BiTrashAlt onClick={() => handleAlertOpen(row)}/>
                        </TableCell>
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
                    count={categories.length}
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
    );
    }

    return (
        <Container component="main" maxWidth="lg">
            <div className={classes.paper}>
            <Grid container item md={6} spacing={2}>
                {new Date(customer.expiry_date).getTime() < new Date().getTime() &&
                <Grid item xs={12} md={12}>
                    <Alert severity="warning">
                        <AlertTitle>Warning</AlertTitle>
                        Your account has been expired. — <strong>Your Expiry Date: {customer.expiry_date}</strong>
                    </Alert>
                </Grid>}
                <Grid item xs={12} md={12}>
                    <Button
                        type="submit"
                        variant="contained"
                        color="primary"
                        className="float-right mb-2"
                        onClick={handleCategoryOpen}
                    >
                        Add Category
                    </Button>
                </Grid>     
                <Grid item xs={12} md={12}>           
                    <AddCategory handleClose={handleCategoryClose} open={categoryOpen} category={category} />
                    {renderCategoriesTable()}
                    <AlertDialogue open={alertOpen} handleClose={() => handleAlertClose(categoryId)} title={alertTitle} message={alertMessage} handleAction={() => handleDelete(categoryId)} />
                    {message && <Snackbar open={messageOpen} autoHideDuration={6000} onClose={() => {setMessageOpen(false); dispatch({ type: categoryConstants.RESET_RESPONSE});}}>
                        <Alert severity="success">{message.message}</Alert>
                    </Snackbar>}
                    {error && 
                    <Snackbar open={errorOpen} autoHideDuration={6000} onClose={() => {setErrorOpen(false); dispatch({ type: categoryConstants.RESET_RESPONSE});}}>
                        <Alert severity="error">
                            {error.split(',').map((message) => {
                                return (<>{message}<br/></>);
                            })}
                        </Alert>
                    </Snackbar>} 
                </Grid>                
            </Grid>
            </div>
        </Container>
    )
}

export default ShowCategories;