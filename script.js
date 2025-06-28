document.addEventListener("DOMContentLoaded", function () {
  const taskInput = document.getElementById("taskInput");
  const addTaskBtn = document.getElementById("addTaskBtn");
  const taskList = document.getElementById("taskList");

  // Load from localStorage
  let tasks = JSON.parse(localStorage.getItem("tasks")) || [];

  function saveTasks() {
    localStorage.setItem("tasks", JSON.stringify(tasks));
  }

  function renderTasks() {
    taskList.innerHTML = "";
    tasks.forEach((task, index) => {
      const li = document.createElement("li");
      li.textContent = task;
      const delBtn = document.createElement("button");
      delBtn.textContent = "❌";
      delBtn.style.marginLeft = "10px";
      delBtn.onclick = () => {
        tasks.splice(index, 1);
        saveTasks();
        renderTasks();
      };
      li.appendChild(delBtn);
      taskList.appendChild(li);
    });
  }

  addTaskBtn.addEventListener("click", () => {
    const task = taskInput.value.trim();
    if (task !== "") {
      tasks.push(task);
      saveTasks();
      renderTasks();
      taskInput.value = "";
    }
  });

  renderTasks();
});