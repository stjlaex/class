<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>
<!-- xslt for subject reports presented in a table with with one to several 
	 assessment grades, a second additional table displays (optional) 
	 short written comments -->


<xsl:output method='html' />

<xsl:template match='text()' />

<xsl:template match="html/body/div">
  <xsl:apply-templates select="student"/>
</xsl:template>

<xsl:template match="student">
<div id='logo'>
<img src="../images/schoollogo.png" width="130px"/>
</div>

<div class='studenthead'>
	<table id='studentdata'>
	  <tr>
		<td>
		  <label>
			Student
		  </label>
		  <xsl:value-of select="forename/value/text()" />&#160;
		  <xsl:value-of select="middlenames/value/text()" />&#160;
		  <xsl:value-of select="surname/value/text()" />
		</td>
	  </tr>
	  <tr>		
		<td>
		  <label>
			Form
		  </label>
		  <xsl:value-of select="registrationgroup/value/text()" />
		  <xsl:text>&#160;&#160;&#160;&#160;&#160;&#160;</xsl:text>
		  <label>Date</label>
		  <xsl:value-of select="reports/publishdate/text()" />
		</td>
	  </tr>
	</table>
</div>

<div class='spacer'></div>

<xsl:apply-templates select="reports"/>

<div class='spacer'></div>

  <table class="grades" id="fulliwdth">
	<tr>
	  <th>
		<xsl:text>Subject</xsl:text>
	  </th>
	  <th><label>Subject teacher's comment.</label></th>
	</tr>
	<xsl:apply-templates select="reports/report/comments/comment" />
  </table>


<div class='spacer'></div>

<div id='footer'>
  <div class='comment'><label>Tutor Comment:</label></div>
  <div class='spacer'></div>
  <div class='signature'><label>Tutor:</label></div>
  <div class='signature'><label>Year Coordinator:</label></div>
</div>

  <hr />
</xsl:template>

<xsl:template match="reports">
  <table class="grades" id="gradesleft">
	<tr>
	  <th>
		<xsl:text>Subject</xsl:text>
	  </th>
	  <xsl:call-template name="assheader" />
	</tr>
	<xsl:apply-templates select="report[(position() mod 2)=1]">
	  <xsl:sort select="course/value/text()" order="descending" case-order="upper-first" />
	</xsl:apply-templates>
  </table>

  <table class="grades" id="gradesright">
	<tr>
	  <th>
		<xsl:text>Subject</xsl:text>
	  </th>
	  <xsl:call-template name="assheader" />
	</tr>
	<xsl:apply-templates select="report[(position() mod 2)=0]">
	  <xsl:sort select="course/value/text()" order="descending" case-order="upper-first" />
	</xsl:apply-templates>
  </table>
</xsl:template>

<xsl:template match="report">
<tr>
  <th>
	<xsl:choose>
	  <xsl:when test="component/value/text()!=' '">
		<xsl:value-of select="component/value/text()" />
	  </xsl:when>
	  <xsl:otherwise>
		<xsl:value-of select="subject/value/text()" />
	  </xsl:otherwise>
	</xsl:choose>
  </th>

  <xsl:call-template name="asscell" />

</tr>
</xsl:template>


<xsl:template name="assheader">
  <xsl:param name="index">1</xsl:param>
  <xsl:variable name="maxindex">
	<xsl:value-of select="count(asstable/ass/label)+1"/>
  </xsl:variable>
  <xsl:variable name='asslabel' select='asstable/ass/label' />
  <xsl:if test="$index &lt; $maxindex">
	<th>
		  <label>
	  <xsl:value-of select="$asslabel[$index]" />  
	  <xsl:text>&#160; </xsl:text>
		  </label>
	</th>
    <xsl:call-template name="assheader">
	  <xsl:with-param name="index" select="$index + 1"/>
    </xsl:call-template>
  </xsl:if>
</xsl:template>

<xsl:template name="asscell">
  <xsl:param name="index">1</xsl:param>
  <xsl:variable name="maxindex">
	<xsl:value-of select="count(../asstable/ass/label)+1"/>
  </xsl:variable>
  <xsl:variable name='asslabel' select='../asstable/ass/label' />

  <xsl:if test="$index &lt; $maxindex">
	<td>
	  <xsl:value-of select="assessments/assessment[printlabel/value=$asslabel[$index]]/result/value/text()" />  
	  <xsl:text>&#160; </xsl:text>
	</td>
    <xsl:call-template name="asscell">
	  <xsl:with-param name="index" select="$index + 1"/>
    </xsl:call-template>
  </xsl:if>
</xsl:template>

<xsl:template match="reports/report/comments/comment">
	<xsl:choose>
	  <xsl:when test="text/value/text()!=' '">
  <tr>
	<th>
	  <xsl:value-of select="../../subject/value/text()" />  
	  <xsl:text>&#160; </xsl:text>
	</th>
	<td class="subject-comment">
	  <xsl:value-of select="text/value/text()" />  
	  <xsl:text>&#160;(</xsl:text>
	  <xsl:value-of select="teacher/value/text()" />
	  <xsl:text>)&#160;</xsl:text>
	</td>
  </tr>
	  </xsl:when>
	  <xsl:otherwise>
	  </xsl:otherwise>
	</xsl:choose>
</xsl:template>


</xsl:stylesheet>
