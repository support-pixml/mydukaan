import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { AppBar, Badge, Button, Divider, IconButton, List, ListItem, makeStyles, SwipeableDrawer, Toolbar, Typography } from '@material-ui/core';
import MoreIcon from '@material-ui/icons/MoreVert';
import LocalMallOutlinedIcon from '@material-ui/icons/LocalMallOutlined';
import { useDispatch, useSelector } from 'react-redux';
import { isUserLoggedIn, signout } from '../../actions/auth';
import { ListTwoTone } from '@material-ui/icons';
import {FaSignOutAlt} from 'react-icons/fa';


const useStyles = makeStyles((theme) => ({
    grow: {
        flexGrow: 1,
    },
    menuButton: {
        marginRight: theme.spacing(2),
    },
    title: {
        display: 'none',
        [theme.breakpoints.up('sm')]: {
        display: 'block',
        },
    },
    sectionDesktop: {
        display: 'none',
        [theme.breakpoints.up('md')]: {
        display: 'flex',
        },
    },
    sectionMobile: {
        display: 'flex',
        [theme.breakpoints.up('md')]: {
        display: 'none',
        },
    },
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
    },
    list: {
        width: 250,
    },
    fullList: {
        width: 'auto',
    },
}));

const Header = () => {
    const classes = useStyles();
    const [sidebarOpen, setSidebarOpen] = useState(false);

    const auth = useSelector(state => state.auth.authData);

    const cartCount = useSelector((state) => state.cart.itemCount);

    const dispatch = useDispatch();
    useEffect(() => {
        if(!auth?.user)
        {
            dispatch(isUserLoggedIn);
        }
    }, [auth]);

    const handleSidebarOpen = () => {
        setSidebarOpen(true);
    };

    const handleSidebarClose = () => {
        setSidebarOpen(false);
    };

    const logout = () => {
        dispatch(signout());
    }

    const list = (
        <div
            className={classes.list}
            role="presentation"
            onClick={handleSidebarClose}
            onKeyDown={handleSidebarClose}
        >
            <List>                    
                <ListItem className="d-block">  
                    <Typography
                        component="strong"
                    >
                        {auth?.user.name}
                    </Typography>  
                    <Typography component="p" variant="caption">
                        {auth?.user.phone}
                    </Typography>   
                </ListItem>
                <Divider />
                <ListItem>
                    <Link to="/show-products" className="w-100">
                        <ListTwoTone /> Products
                    </Link>
                </ListItem>
                <Divider />
                {(auth?.user.role === '1' || auth?.user.role === '2') && <> 
                <ListItem>
                    <Link to="/show-categories" className="w-100">
                        <ListTwoTone /> Categories
                    </Link>
                </ListItem>
                <Divider /></>}
                {auth?.user.role === '1' && <>
                <ListItem>
                    <Link to="/show-users" className="w-100">
                        <ListTwoTone /> Users
                    </Link>
                </ListItem>
                <Divider /></>}
                {(auth?.user.role === '1' || auth?.user.role === '2') ? 
                <> 
                    <ListItem>
                        <Link to="/show-orders" className="w-100"> 
                            <ListTwoTone /> Orders
                        </Link>
                    </ListItem>
                    <Divider />
                    <ListItem>
                        <Link to="/show-temp-orders" className="w-100"> 
                            <ListTwoTone /> Salesman's Orders
                        </Link>
                    </ListItem>
                </> : 
                <> 
                <ListItem>
                    <Link to="/show-temp-orders" className="w-100"> 
                        <ListTwoTone /> Orders
                    </Link>
                </ListItem>
                <Divider />
            </>
                }
                <ListItem>
                    <Button color="primary" disableElevation
                        className="btn btn-block btn-primary"
                        variant="contained"
                        onClick={logout}
                    >
                        <FaSignOutAlt />&nbsp;&nbsp;Sign Out
                    </Button>
                </ListItem>
            </List>
        </div>
    );

    const sidebar = ( 
        <SwipeableDrawer
            anchor="right"
            onClose={handleSidebarClose}
            onOpen={handleSidebarOpen}
            open={sidebarOpen}
        >
            {list}
        </SwipeableDrawer>
    );

    return (
        <div className={classes.grow}>
        <AppBar position="fixed" color="inherit">
            <Toolbar>
            <Link to="/" className="">
                {customer.logo !== null ? 
                <img className="" src={`uploads/customers/${customer.logo}`} alt="Logo" height="50" />
                :
                <Typography variant="h4">
                    {customer.company_name} 
                </Typography>                
                }
            </Link>
            <div className={classes.grow} />
            <div className={classes.sectionDesktop}>
                {auth?.user &&
                <IconButton color="inherit" onClick={handleSidebarOpen}
                    aria-label="setting"
                    aria-haspopup="true"
                    className={classes.button}>
                    <MoreIcon />
                </IconButton>
                }
            </div>
            <div className={classes.sectionMobile}>
                {cartCount > 0 && 
                <Link to="/checkout" className="">
                    <IconButton aria-label="show new notifications" color="inherit">
                        <Badge badgeContent={cartCount} color="secondary">
                            <LocalMallOutlinedIcon />
                        </Badge>
                    </IconButton>
                </Link>
                }
                <IconButton
                aria-label="show more"
                aria-haspopup="true"
                onClick={handleSidebarOpen}
                color="inherit"
                >
                <MoreIcon />
                </IconButton>
            </div>
            </Toolbar>
        </AppBar>
        {sidebar}
        </div>
    );
}

export default Header;