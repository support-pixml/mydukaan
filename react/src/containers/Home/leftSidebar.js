import { Grid, List, ListItem, makeStyles } from '@material-ui/core';
import React from 'react';
import { Link } from 'react-router-dom';
import ArrowRightAltIcon from '@material-ui/icons/ArrowRightAlt';
import { useSelector } from 'react-redux';


const useStyles = makeStyles((theme) => ({
    root: {
        flexGrow: 1,
        maxWidth: 752,
        // position: -webkit-sticky,
        position: 'sticky',
        height: '100%',
        top: '90px',
        maxHeight: '-webkit-calc(100vh - 90px)',
        maxHeight: 'calc(100vh - 90px)',
        overFlow: 'hidden',
    },
    demo: {
        backgroundColor: theme.palette.background.default,
    },
}));

const leftSidebar = () => {
    const classes = useStyles();
    const categories = useSelector((state) => state.category.categories);

    return (
        <div className={classes.root}>
            <Grid container spacing={2}>
                <Grid item xs={12} md={12}>
                <div className={classes.demo}>
                    <List>
                        {categories.map((category) => (
                        <ListItem key={category.long_id}>
                            <Link to={`#${category.slug}`} spy={true} smooth={true}>
                                {category.name}
                            </Link>
                        </ListItem>
                        ))}
                        <ListItem>
                            <Link
                                to="/categories"
                            >
                                Categories <ArrowRightAltIcon />
                            </Link>
                        </ListItem>
                    </List>
                </div>
                </Grid>
            </Grid>
        </div>
    )
}

export default leftSidebar;