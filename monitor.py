#!/usr/bin/python

import subprocess, time, datetime, sys, sqlite3, argparse

class Monitor():
  def __init__(self):
    # Defaults will be overwritten by parseArgs
    self.settings = {"freq":     60,
                     "retrys":   3,
                     "timeout":  5,
                     "address":  "8.8.8.8",
                     "path":     "/home/pi/internet-monitor/",
                     "database": "connectivity.sqlite",
                     "errors":   "/home/pi/monitor-errors.txt"}

    self.parseArgs()

    self.now = datetime.datetime.now()

    self.connection = sqlite3.connect(self.settings["path"] + self.settings["database"])

    self.setupDatabase()

    self.sleep()

    self.run()

  def setupDatabase(self):
    c = self.connection.cursor()

    c.execute("SELECT count(*) FROM sqlite_master WHERE type='table' AND name='connected';")

    exists = c.fetchone()[0]
    if not exists:
      print("Creating table")
      c.execute("CREATE TABLE connected (datetime INTEGER PRIMARY KEY, value INTEGER);")
      c.execute("CREATE INDEX connected_value ON connected (value);")

    self.connection.commit()

  def run(self):
    while True:
      self.now = datetime.datetime.now()

      res = self.checkConnection()

      self.saveResult(res)

      self.sleep()

  def checkConnection(self):
    for i in range(self.settings["retrys"]):
      res = subprocess.call(["/bin/ping",
                             "-c1",
                             "-w" + str(self.settings["timeout"]), self.settings["address"]],
                             stdout = subprocess.PIPE)
      if not res:
        return 1

    return 0

  def saveResult(self, res):
    c = self.connection.cursor()

    c.execute("INSERT INTO connected (datetime, value) VALUES ("
            + str(int(time.mktime(self.now.timetuple())))
            + ", " + str(int(res)) + ");")

    self.connection.commit()

  def sleep(self):
    delta  = datetime.timedelta(minutes = 1)
    future = self.now + delta

    future = future.replace(second = 0, microsecond = 0)

    sleep = (future - self.now).total_seconds()

    if sleep > 0:
      time.sleep(sleep)

  def parseArgs(self):
    parser = argparse.ArgumentParser(description="A command line tool to monitor internet connectivity.")

    parser.add_argument("--address",  "-a", help="The remote address toping.",                      required=False)
    parser.add_argument("--freq",     "-f", help="How often to check for a connection in seconds.", required=False, type=int)
    parser.add_argument("--timeout",  "-t", help="The timeout for the connectivity check.",         required=False, type=int)
    parser.add_argument("--retrys",   "-r", help="The number of retrys if first check fails",       required=False, type=int)
    parser.add_argument("--database", "-d", help="The database to store restults in.",              required=False)
    parser.add_argument("--errors",   "-e", help="The file to log any errors in.",                  required=False)

    args = parser.parse_args()

    v = vars(args)

    for arg in v:
        if v[arg] is not None:
            self.settings[arg] = v[arg]

m = Monitor()
