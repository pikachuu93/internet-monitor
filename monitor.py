#!/usr/bin/python

import subprocess, time, datetime, sys, sqlite3

class Monitor():
  def __init__(self, args = []):
    # Defaults will be overwritten by parseArgs
    self.freq    = 60
    self.retrys  = 3
    self.timeout = 5
    self.address = "8.8.8.8"
    self.path    = "/home/pi/"
    self.dbName  = "connectivity.sqlite"

    self.parseArgs(args)

    self.now = datetime.datetime.now()

    self.connection = sqlite3.connect(self.path + self.dbName)

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

    self.connection.commit()
    

  def run(self):
    while True:
      self.now = datetime.datetime.now()

      res = self.checkConnection()

      self.saveResult(res)

      self.sleep()

  def checkConnection(self):
    for i in range(self.retrys):
      res = subprocess.call(["/bin/ping", "-c1", "-w" + str(self.timeout), self.address],
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

  def parseArgs(self, argsIn):
    args = list(argsIn)

m = Monitor()
