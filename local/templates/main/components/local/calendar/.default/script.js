const App = {
    year: (new Date()).getFullYear(),
    month: (new Date()).getMonth(),
    cellDate: '',
    idTask:'',
    init() {
        this.printCalendar();
        this.bindEvents();
        this.printYears();
        this.printMonth();
        this.getTasks();
    },
    bindEvents() {
        $("#js-save-task").click(this.saveTask);
        $("#js-update-task").click(this.updateTask);
        $(".cell").click(this.openTaskForm);
        $("#year,#month").change(this.changeSelects);
    },
    changeSelects(){
        App.year = $("#year").val();
        App.month = $("#month").val()-1;
        App.printCalendar();
        App.getTasks();
    },
    printYears() {
        for (let i = this.year-5; i < this.year+6; i++)
        {
            $("#year").append(`<option ${(this.year ==i)?'selected':''} value='${i}'>${i}</option>`)
        }
    },
    printMonth(){
        const month = ["Январь","Феравль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"];
        for (let i = 0; i < 12; i++) {
            $("#month").append(`<option ${(this.month == i) ? 'selected' : ''} value='${i+1}'>${month[i]}</option>`)
        }
    },
    getTasks()
    {
        $.post("/api/rest-component/local/calendar/get/",{year:this.year,month:this.month},(res)=>{
            const items = res.data.items;
            for(let i = 0; i <items.length; i ++)
            {
                App.setTask(items[i].date, items[i]);
            }
        })
    },
    updateTask()
    {
        let data = $("#update-form-items").serialize() + `&id=${App.idTask}`;
        $.post("/api/rest-component/local/calendar/updateTask/", data, function (res) {
            if (res.data.success) {
                App.updateTaskItem(res.data.item);
                $("#update-form-items")[0].reset();
                $("#update-form").modal("hide");
            }
            else {
                alert("Возникли ошибки при сохранений")
            }
        });
    },
    openTask(e, el) {
        e.stopPropagation();
        App.idTask = $(el).data('id');
        $.post('/api/rest-component/local/calendar/getTask/', { id: App.idTask},(res)=>{
            const updateForm = $("#update-form");
            const item = res.data.item;
            for (let prop in item) {
                $(updateForm).find(`[name=${prop}]`).val(item[prop]);
            }
            $(updateForm).modal('show');
        })
    },
    openTaskForm() {
        App.cellDate = $(this).data('date');
        $("#save-form").modal("show");
    },
    getFormatName(name) {
        if (name.length > 8) {
            return name.slice(0, 8) + "...";
        }
        else {
            return name;
        }
    },
    setTask(date,item) {
        $(`[data-date='${date}']`).append(`<div class="task" data-id="${item.id}" onclick="App.openTask(event,this)">${item.name}</div>`);
    },
    updateTaskItem(item){
        $(`[data-id=${item.id}]`).html(this.getFormatName(item.name));
    },
    saveTask() {
        let data = $("#save-form-items").serialize() + `&date=${App.cellDate}`;
        $.post("/api/rest-component/local/calendar/save/", data, function (res) {
            if (res.data.success) {
                App.setTask(App.cellDate,res.data);
                $("#save-form-items")[0].reset();
                $("#save-form").modal("hide");
            }
            else
            {
                alert("Возникли ошибки при сохранений")
            }
        });
    },
    getMouthDays(year, month) {
        let date = new Date(Date.UTC(year, month, 1));
        let days = [];
        while (date.getUTCMonth() == month) {
            let acrulalDate = new Date(date);
            const dateRaw = acrulalDate.toISOString().split('T')[0].split('-');
            days.push({
                day: acrulalDate.getDate(),
                weekDay: acrulalDate.getDay(),
                date: dateRaw[2] + "." + dateRaw[1] + "." + dateRaw[0]
            });
            date.setUTCDate(date.getUTCDate() + 1);
        }
        return days;
    },
    printCalendar() {
        $(".table-cells").html("");
        let days = this.getMouthDays(this.year, this.month);
        let calendarCounter = 1;
        let firstDay = days[0];
        for (let i = 0; i < 5; i++) {
            $(".table-cells").append(`<tr class='tr${i}'></tr>`);
            for (let j = 0; j < 7; j++) {
                let dayBlockHtml = "";
                if (calendarCounter >= firstDay.weekDay && days.length > 0) {
                    let dayItem = days.shift();
                    dayBlockHtml = `<div class="cell" data-date="${dayItem.date}"><div class="day">${dayItem.day}</div></div>`;
                }
                calendarCounter++;
                $(`.tr${i}`).append(`<td class='cell_border td${i}${j}'>${dayBlockHtml}</td>`);
            }
        }
    }
}
$(document).ready(function(){
    App.init();
})