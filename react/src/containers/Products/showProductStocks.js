import { Backdrop, Fade, makeStyles, Modal, Paper, Table, TableBody, TableCell, TableContainer, TableFooter, TableHead, TablePagination, TableRow, Typography } from '@material-ui/core';
import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { getProductStocks } from '../../actions/products';

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
        width: '650px'
    },
    table: {
        maxHeight: '450px',
    }
}));

const ShowProductStocks = ({handleClose, open, product}) => {
    const classes = useStyles();

    const dispatch = useDispatch();
    const [page, setPage] = useState(0);
    const [rowsPerPage, setRowsPerPage] = useState(10);    

    useEffect(() => {
        if(product)
        {
            dispatch(getProductStocks(product.long_id));
        }
    }, [product]);
    
    const product_stocks = useSelector((state) => state.product.productStocks);
    
    const renderProductStocksTable = () => {
        const emptyRows = rowsPerPage - Math.min(rowsPerPage, product_stocks.length - page * rowsPerPage);
    
        const handleChangePage = (event, newPage) => {
            setPage(newPage);
        };
    
        const handleChangeRowsPerPage = (event) => {
            setRowsPerPage(parseInt(event.target.value, 10));
            setPage(0);
        };
        return (
            <TableContainer className={classes.table} component={Paper}>
                <Table  aria-label="simple table">
                    <TableHead>
                    <TableRow>
                        <TableCell>Sr. No.</TableCell>
                        <TableCell>Document No.</TableCell>
                        <TableCell>Purchased Qty</TableCell>
                        <TableCell>Stock</TableCell>
                        <TableCell>Previous Stock</TableCell>
                        <TableCell>Created At</TableCell>
                    </TableRow>
                    </TableHead>
                    <TableBody>
                    {(rowsPerPage > 0
                        ? product_stocks.slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage)
                        : product_stocks
                    ).map((stock_data, index) => {
                        return (
                        <TableRow key={index}>
                        <TableCell component="th" scope="row">
                            {index+1}
                        </TableCell>
                        <TableCell>{stock_data.document_id}</TableCell>
                        <TableCell>{stock_data.original_stock == 0 ? '--' : stock_data.original_stock}</TableCell>
                        <TableCell>{stock_data.stock}</TableCell>
                        <TableCell>{stock_data.total_stock_last_time > 0 && stock_data.min_stock_qty < stock_data.total_stock_last_time ? <Typography color='error' variant='caption'>{stock_data.total_stock_last_time}</Typography> : stock_data.total_stock_last_time}</TableCell>
                        <TableCell>{stock_data.created_at}</TableCell>
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
                        count={product_stocks.length}
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
                    {product && <Typography component="h1" variant="h5">
                        {product.name}
                    </Typography>}
                    {product_stocks ? renderProductStocksTable() : 
                    <Typography component="h3" variant="h5">
                        No Stock Data Found.
                    </Typography>}
                </div>
            </Fade>
        </Modal>
    );
}

export default ShowProductStocks;