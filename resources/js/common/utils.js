import moment from 'moment';
// Use the relativeTime plugin

export const useDateFormatter = () => {
    const formatDate = (date, format = 'D MMM YYYY h:mmA') => {
      return moment(date).format(format);
    };
  
    const formatRelativeTime = (date) => {
      return moment(date).fromNow();
    };
    const isCloseDateToday = (date) => {
      const formattedDate = moment(date).format('DD-MM-YYYY');
      const formattedCurrentDate = moment().format('DD-MM-YYYY');
  
      return formattedDate === formattedCurrentDate;
    }
    return { formatDate, formatRelativeTime, isCloseDateToday };
  }

export const getCurrentDateString = () => {
    const today = new Date();
    const yyyy = today.getFullYear();
    let mm = today.getMonth() + 1; // Months start at 0!
    let dd = today.getDate();

    // Add leading zeros to month and day if they are less than 10
    mm = mm < 10 ? '0' + mm : mm;
    dd = dd < 10 ? '0' + dd : dd;

    const formattedToday = `${yyyy}-${mm}-${dd}`;
    return formattedToday;
};

export const getEndMonthDateString = () => {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = today.getMonth() + 1; // Months start at 0 in JavaScript
    const lastDay = new Date(yyyy, mm, 0).getDate(); // 0th day of the next month is the last day of the current month

    const formattedLastDay = `${yyyy}-${mm < 10 ? '0' + mm : mm}-${lastDay < 10 ? '0' + lastDay : lastDay}`;
    return formattedLastDay;
};


export const emailIsValid = (email) => {
    let regEx = /^(?!.*@)((^[^.])[a-z0-9.!#$%&'*+\-/=?^_`{|}~"]*)*([^.]$)/;
    return regEx.test(email);
}

export const debounce = (func, wait) => {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};
