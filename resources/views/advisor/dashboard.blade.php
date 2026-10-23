<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advisor Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/advisor.css') }}">
</head>
<body>
    
<!-- Main Layout of page Right side -->
    <div class="layout">

    <!-- NaBar (sidebar)-->
        <nav class="sidebar">

            <!-- Top NavBar-->
            <div class="sidebar-top">
                <div class="brand">
                    <span class="brand-mark">DP</span>
                    <span class="brand-name">Degree-Planner</span>
                </div>
            </div>

            <!-- Middle NavBar-->
             <div class="sidebar-middle">
                <p class="nav-label">Menu</p>
                <ul class="nav-list">
                    <li><a href="" class="button">Dashboard</a></li>
                    <li><a href="{{ route('advisor.students.index') }}" class="button">My Students</a></li>
                    <li><a href="" class="button">Student Planner</a></li>
                    <li><a href="" class="button">Notifications</a></li>
                    <li><a href="" class="button">Calander</a></li>
                    <li><a href="" class="button">Inbox</a></li>
                    <br>
                    <li><a href="" class="">Settings</a></li>
                </ul>
             </div>

              <!-- Bottom NavBar-->
               <div class="sidebar-bottom">
                    <div class="user">
                        <span class="user-avatar">TA</span>
                        <div>
                            <p class="user-name">{{ auth()->user()->name }}</p>
                            <p class="user-role">{{ ucfirst(auth()->user()->role) }}</p>
                        </div>
                    </div>

                    <form action="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout">Log out</button>
                    </form>
                </div>
        </nav>

    <!-- Right side: header and page content together -->
        <div class="main">

            <!-- Page header  -->
            <header class="header">
                <div class="container">
                    <div>
                        <h1>Advisor Dashboard</h1>
                        <p class="subtitle">{{ auth()->user()->name }} · {{ auth()->user()->role }}</p>
                    </div>
                    <!-- Log out Button -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-button">Log out</button>
            </form>
                </div>
            </header>

            <!-- Page Body  -->
            <main class="page-body">
                <section class="stats">
                    <div class="card">
                        <p class="card-label">Assigned students</p>
                        <p class="card-number">{{ $studentCount ?? 0 }}</p>
                    </div>
                    <div class="card">
                        <p class="card-label">Plans awaiting review</p>
                        <p class="card-number">0</p>
                    </div>
                    <div class="card">
                        <p class="card-label">Needs attention</p>
                        <p class="card-number">0</p>
                    </div>
                </section>

                <section class="sections">
                    <div class="card">
                        <h2>Plans awaiting review</h2>
                        <p class="empty">No plans waiting for review.</p>
                    </div>
                    <div class="card">
                        <h2>Students needing attention</h2>
                        <p class="empty">No students flagged.</p>
                    </div>
                </section>

                <section>
                    <div class="card">
                        <p>Schedule will go here!!</p>
                    </div>
                </section>

            </main>
        </div>
    </div>

</body>
</html>